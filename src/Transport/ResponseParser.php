<?php

namespace Smalot\Cups\Transport;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Smalot\Cups\Model\Operations;

/**
 * Class ResponseParser
 *
 * @package Smalot\Cups\Transport
 */
class ResponseParser
{

    /**
     * @var string
     */
    protected string $content = '';

    /**
     * @var array
     */
    protected array $body = [];

    /**
     * @var int
     */
    protected int $index = 0;

    /**
     * @var int
     */
    protected int $offset = 0;

    /**
     * @var mixed
     */
    protected mixed $collection; // RFC3382

    /**
     * @var array
     */
    protected array $collection_key = []; // RFC3382

    /**
     * @var int
     */
    protected int $collection_depth = -1; // RFC3382

    /**
     * @var bool
     */
    protected bool $end_collection = false; // RFC3382

    /**
     * @var array
     */
    protected array $collection_nbr = []; // RFC3382

    /**
     * @var string
     */
    protected string $attribute_name = '';

    /**
     * @var string
     */
    protected string $last_attribute_name = '';

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     */
    public function parse(ResponseInterface $response): Response
    {
        // Reset properties.
        $this->reset();

        // Run parsing.
        $this->content = $response->getBody()->getContents();
        $ipp_version = $this->parseIppVersion();
        $status_code = $this->parseStatusCode();
        $request_id = $this->parseRequestID();
        $body = $this->parseBody();

        return $this->generateResponse($ipp_version, $status_code, $request_id, $body);
    }

    /**
     *
     */
    protected function reset(): void
    {
        $this->offset = 0;
        $this->index = 0;
        $this->collection = null;
        $this->collection_key = [];
        $this->collection_depth = -1;
        $this->end_collection = false;
        $this->collection_nbr = [];
        $this->attribute_name = '';
        $this->last_attribute_name = '';
    }

    /**
     * @param string $ipp_version
     * @param string $status_code
     * @param int    $request_id
     * @param array  $body
     *
     * @return Response
     */
    protected function generateResponse(string $ipp_version, string $status_code, int $request_id, array $body): Response
    {
        return new Response($ipp_version, $status_code, $request_id, $body);
    }

    /**
     * @return string
     */
    protected function parseIppVersion(): string
    {
        $text = (ord($this->content[$this->offset]) * 256) + ord($this->content[$this->offset + 1]);
        $this->offset += 2;

        if ($text == 0x0101) {
            $ipp_version = '1.1';
        } else {
            $ipp_version =
                sprintf(
                    '%u.%u (Unknown)',
                    ord($this->content[$this->offset]) * 256,
                    ord($this->content[$this->offset + 1])
                );
        }

        return $ipp_version;
    }

    /**
     * @return string
     */
    protected function parseStatusCode(): false|string
    {
        $status_code = (ord($this->content[$this->offset]) * 256) + ord($this->content[$this->offset + 1]);
        $status = 'NOT PARSED';
        $this->offset += 2;

        if (strlen($this->content) < $this->offset) {
            return false;
        }

        if ($status_code < 0x00FF) {
            $status = 'successfull';
        } elseif ($status_code < 0x01FF) {
            $status = 'informational';
        } elseif ($status_code < 0x02FF) {
            $status = 'redirection';
        } elseif ($status_code < 0x04FF) {
            $status = 'client-error';
        } elseif ($status_code < 0x05FF) {
            $status = 'server-error';
        }

        switch ($status_code) {
            case 0x0000:
                $status = 'successfull-ok';
                break;

            case 0x0001:
                $status = 'successful-ok-ignored-or-substituted-attributes';
                break;

            case 0x002:
                $status = 'successful-ok-conflicting-attributes';
                break;

            case 0x0400:
                $status = 'client-error-bad-request';
                break;

            case 0x0401:
                $status = 'client-error-forbidden';
                break;

            case 0x0402:
                $status = 'client-error-not-authenticated';
                break;

            case 0x0403:
                $status = 'client-error-not-authorized';
                break;

            case 0x0404:
                $status = 'client-error-not-possible';
                break;

            case 0x0405:
                $status = 'client-error-timeout';
                break;

            case 0x0406:
                $status = 'client-error-not-found';
                break;

            case 0x0407:
                $status = 'client-error-gone';
                break;

            case 0x0408:
                $status = 'client-error-request-entity-too-large';
                break;

            case 0x0409:
                $status = 'client-error-request-value-too-long';
                break;

            case 0x040A:
                $status = 'client-error-document-format-not-supported';
                break;

            case 0x040B:
                $status = 'client-error-attributes-or-values-not-supported';
                break;

            case 0x040C:
                $status = 'client-error-uri-scheme-not-supported';
                break;

            case 0x040D:
                $status = 'client-error-charset-not-supported';
                break;

            case 0x040E:
                $status = 'client-error-conflicting-attributes';
                break;

            case 0x040F:
                $status = 'client-error-compression-not-supported';
                break;

            case 0x0410:
                $status = 'client-error-compression-error';
                break;

            case 0x0411:
                $status = 'client-error-document-format-error';
                break;

            case 0x0412:
                $status = 'client-error-document-access-error';
                break;

            case 0x0413: // RFC3380
                $status = 'client-error-attributes-not-settable';
                break;

            case 0x0500:
                $status = 'server-error-internal-error';
                break;

            case 0x0501:
                $status = 'server-error-operation-not-supported';
                break;

            case 0x0502:
                $status = 'server-error-service-unavailable';
                break;

            case 0x0503:
                $status = 'server-error-version-not-supported';
                break;

            case 0x0504:
                $status = 'server-error-device-error';
                break;

            case 0x0505:
                $status = 'server-error-temporary-error';
                break;

            case 0x0506:
                $status = 'server-error-not-accepting-jobs';
                break;

            case 0x0507:
                $status = 'server-error-busy';
                break;

            case 0x0508:
                $status = 'server-error-job-canceled';
                break;

            case 0x0509:
                $status = 'server-error-multiple-document-jobs-not-supported';
                break;

            default:
                break;
        }

        return $status;
    }

    /**
     * @return int
     */
    protected function parseRequestID(): float|int
    {
        $request_id = $this->interpretInteger(substr($this->content, $this->offset, 4));
        $this->offset += 4;

        return $request_id;
    }

    /**
     * @return array
     */
    protected function parseBody(): array
    {
        $j = -1;
        $this->index = 0;

        for ($i = $this->offset; $i < strlen($this->content); $i = $this->offset) {
            $tag = ord($this->content[$this->offset]);

            if ($tag > 0x0F) {
                $this->readAttribute($j);
                $this->index++;
                continue;
            }

            $j += 1;
            switch ($tag) {
                case 0x01:
                    $this->body[$j]['attributes'] = 'operation-attributes';
                    $this->index = 0;
                    $this->offset += 1;
                    break;
                case 0x02:
                    $this->body[$j]['attributes'] = 'job-attributes';
                    $this->index = 0;
                    $this->offset += 1;
                    break;
                case 0x03:
                    $this->body[$j]['attributes'] = 'end-of-attributes';

                    return $this->body;
                case 0x04:
                    $this->body[$j]['attributes'] = 'printer-attributes';
                    $this->index = 0;
                    $this->offset += 1;
                    break;
                case 0x05:
                    $this->body[$j]['attributes'] = 'unsupported-attributes';
                    $this->index = 0;
                    $this->offset += 1;
                    break;
                default:
                    $this->body[$j]['attributes'] = sprintf(_('0x%x (%u) : attributes tag Unknown (reserved for future versions of IPP'),
                      $tag,
                      $tag
                    );
                    $this->index = 0;
                    $this->offset += 1;
                    break;
            }
        }

        return $this->body;
    }

    protected function readAttribute($attributes_type): void
    {
        $tag = ord($this->content[$this->offset]);

        $this->offset += 1;
        $j = $this->index;

        $tag = $this->readTag($tag);

        switch ($tag) {
            case 'begCollection': //RFC3382 (BLIND CODE)
                if ($this->end_collection) {
                    $this->index--;
                }
                $this->end_collection = false;
                $this->body[$attributes_type][$j]['type'] = 'collection';
                $this->readAttributeName($attributes_type, $j);
                if (!$this->body[$attributes_type][$j]['name']) { // it is a multi-valued collection
                    $this->collection_depth++;
                    $this->index--;
                    $this->collection_nbr[$this->collection_depth]++;
                } else {
                    $this->collection_depth++;
                    if ($this->collection_depth == 0) {
                        $this->collection = (object)'collection';
                    }
                    if (array_key_exists($this->collection_depth, $this->collection_nbr)) {
                        $this->collection_nbr[$this->collection_depth]++;
                    } else {
                        $this->collection_nbr[$this->collection_depth] = 0;
                    }
                    unset($this->end_collection);

                }
                $this->readValue($attributes_type, $j);
                break;
            case 'endCollection': //RFC3382 (BLIND CODE)
                $this->body[$attributes_type][$j]['type'] = 'collection';
                $this->readAttributeName($attributes_type, $j, 0);
                $this->readValue($attributes_type, $j, 0);
                $this->collection_depth--;
                $this->collection_key[$this->collection_depth] = 0;
                $this->end_collection = true;
                break;
            case 'memberAttrName': // RFC3382 (BLIND CODE)
                $this->body[$attributes_type][$j]['type'] = 'memberAttrName';
                $this->index--;
                $this->readCollection($attributes_type, $j);
                break;

            default:
                $this->collection_depth = -1;
                $this->collection_key = [];
                $this->collection_nbr = [];
                $this->body[$attributes_type][$j]['type'] = $tag;
                $attribute_name = $this->readAttributeName($attributes_type, $j);
                if (!$attribute_name) {
                    $attribute_name = $this->attribute_name;
                } else {
                    $this->attribute_name = $attribute_name;
                }
                $this->readValue($attributes_type, $j);
                $this->body[$attributes_type][$j]['value'] = $this->interpretAttribute($attribute_name, $tag, $this->body[$attributes_type][$j]['value']);
                break;

        }
    }

    protected function readTag($tag): string
    {
        switch ($tag) {
            case 0x10:
                $tag = 'unsupported';
                break;
            case 0x11:
                $tag = 'reserved for "default"';
                break;
            case 0x12:
                $tag = 'unknown';
                break;
            case 0x13:
                $tag = 'no-value';
                break;
            case 0x15: // RFC 3380
                $tag = 'not-settable';
                break;
            case 0x16: // RFC 3380
                $tag = 'delete-attribute';
                break;
            case 0x17: // RFC 3380
                $tag = 'admin-define';
                break;
            case 0x20:
                $tag = 'IETF reserved (generic integer)';
                break;
            case 0x21:
                $tag = 'integer';
                break;
            case 0x22:
                $tag = 'boolean';
                break;
            case 0x23:
                $tag = 'enum';
                break;
            case 0x30:
                $tag = 'octetString';
                break;
            case 0x31:
                $tag = 'datetime';
                break;
            case 0x32:
                $tag = 'resolution';
                break;
            case 0x33:
                $tag = 'rangeOfInteger';
                break;
            case 0x34: //RFC3382 (BLIND CODE)
                $tag = 'begCollection';
                break;
            case 0x35:
                $tag = 'textWithLanguage';
                break;
            case 0x36:
                $tag = 'nameWithLanguage';
                break;
            case 0x37: //RFC3382 (BLIND CODE)
                $tag = 'endCollection';
                break;
            case 0x40:
                $tag = 'IETF reserved (generic character-string)';
                break;
            case 0x41:
                $tag = 'textWithoutLanguage';
                break;
            case 0x42:
                $tag = 'nameWithoutLanguage';
                break;
            case 0x43:
                $tag = 'IETF reserved for future';
                break;
            case 0x44:
                $tag = 'keyword';
                break;
            case 0x45:
                $tag = 'uri';
                break;
            case 0x46:
                $tag = 'uriScheme';
                break;
            case 0x47:
                $tag = 'charset';
                break;
            case 0x48:
                $tag = 'naturalLanguage';
                break;
            case 0x49:
                $tag = 'mimeMediaType';
                break;
            case 0x4A: // RFC3382 (BLIND CODE)
                $tag = 'memberAttrName';
                break;
            case 0x7F:
                $tag = 'extended type';
                break;
            default:
                if ($tag >= 0x14 && $tag < 0x15 && $tag > 0x17 && $tag <= 0x1f) {
                    $tag = 'out-of-band';
                } elseif (0x24 <= $tag && $tag <= 0x2f) {
                    $tag = 'new integer type';
                } elseif (0x38 <= $tag && $tag <= 0x3F) {
                    $tag = 'new octet-stream type';
                } elseif (0x4B <= $tag && $tag <= 0x5F) {
                    $tag = 'new character string type';
                } elseif ((0x60 <= $tag && $tag < 0x7f) || $tag >= 0x80) {
                    $tag = 'IETF reserved for future';
                } else {
                    $tag = sprintf('UNKNOWN: 0x%x (%u)', $tag, $tag);
                }
                break;
        }

        return $tag;
    }

    protected function readCollectionValue(&$output): bool
    {
        if (isset($this->content[$this->offset + 1])) {
            $length = ord($this->content[$this->offset]) * 256 + ord($this->content[$this->offset + 1]);
            $this->offset += 2;
            $output = '';

            for ($i = 0; $i < $length; $i++) {
                if (!isset($this->content[$this->offset])) {
                    return false;
                }

                $output .= $this->content[$this->offset];
                $this->offset += 1;
            }
            return true;
        }
        return false;
    }

    /**
     * @param $attributes_type
     * @param $j
     *
     * @return void
     */
    protected function readCollection($attributes_type, $j): void
    {
        $collection_name = $attribute_name = $collection_value = $value = '';
        if (!$this->readCollectionValue($collection_name)) {
            return;
        }
        if (!$this->readCollectionValue($attribute_name)) {
            return;
        }


        if ($attribute_name == '') {
            $attribute_name = $this->last_attribute_name;
            $this->collection_key[$this->collection_depth]++;
        } else {
            $this->collection_key[$this->collection_depth] = 0;
        }
        $this->last_attribute_name = $attribute_name;

        $tag = $this->readTag(ord($this->content[$this->offset]));
        $this->offset++;
        $type = $tag;

        if (!$this->readCollectionValue($collection_value)) {
            return;
        }

        if (!$this->readCollectionValue($value)) {
            return;
        }

        $object = &$this->collection;
        for ($i = 0; $i <= $this->collection_depth; $i++) {
            $indice = '_indice'.$this->collection_nbr[$i];
            if (!isset($object->$indice)) {
                $object->$indice = (object)'indice';
            }
            $object = &$object->$indice;
        }

        $value_key = '_value'.$this->collection_key[$this->collection_depth];
        $col_name_key = '_collection_name'.$this->collection_key[$this->collection_depth];
        $col_val_key = '_collection_value'.$this->collection_key[$this->collection_depth];

        $attribute_value = $this->interpretAttribute($attribute_name, $tag, $value);
        $attribute_name = str_replace('-', '_', $attribute_name);

        $object->$attribute_name->_type = $type;
        $object->$attribute_name->$value_key = $attribute_value;
        $object->$attribute_name->$col_name_key = $collection_name;
        $object->$attribute_name->$col_val_key = $collection_value;

        $this->body[$attributes_type][$j]['value'] = $this->collection;
    }

    protected function readAttributeName($attributes_type, $j, $write = 1): false|string
    {
        $name_length = ord($this->content[$this->offset]) * 256 + ord($this->content[$this->offset + 1]);
        $this->offset += 2;
        $name = '';

        for ($i = 0; $i < $name_length; $i++) {
            if ($this->offset >= strlen($this->content)) {
                return false;
            }
            $name .= $this->content[$this->offset];
            $this->offset += 1;
        }

        if ($write) {
            $this->body[$attributes_type][$j]['name'] = $name;
        }

        return $name;
    }

    protected function readValue($attributes_type, $j, $write = 1): false|string
    {
        $value_length = ord($this->content[$this->offset]) * 256 + ord($this->content[$this->offset + 1]);
        $this->offset += 2;
        $value = '';

        for ($i = 0; $i < $value_length; $i++) {
            if ($this->offset >= strlen($this->content)) {
                return false;
            }
            $value .= $this->content[$this->offset];
            $this->offset += 1;
        }

        if ($write) {
            $this->body[$attributes_type][$j]['value'] = $value;
        }

        return $value;
    }

    protected function interpretAttribute($attribute_name, $type, $value)
    {
        switch ($type) {
            case 'integer':
                $value = $this->interpretInteger($value);
                break;
            case 'rangeOfInteger':
                $value = $this->interpretRangeOfInteger($value);
                break;
            case 'boolean':
                $value = ord($value);
                if ($value == 0x00) {
                    $value = false;
                } else {
                    $value = true;
                }
                break;
            case 'datetime':
                $value = $this->interpretDateTime($value);
                break;
            case 'enum':
                $value = $this->interpretEnum($attribute_name, $value); // must be overwritten by children
                break;
            case 'resolution':
                $unit = $value[8];
                $value = $this->interpretRangeOfInteger(substr($value, 0, 8));
                if ($unit == chr(0x03)) {
                    $unit = 'dpi';
                } elseif ($unit == chr(0x04)) {
                    $unit = 'dpc';
                }
                $value = $value.' '.$unit;
                break;
            default:
                break;
        }

        return $value;
    }

    protected function interpretInteger($value): float|int
    {
        // They are _signed_ integers.
        $value_parsed = 0;
        for ($i = strlen($value); $i > 0; $i--) {
            $value_parsed += ((1 << (($i - 1) * 8)) * ord($value[strlen($value) - $i]));
        }

        if ($value_parsed >= 2147483648) {
            $value_parsed -= 4294967296;
        }

        return $value_parsed;
    }

    protected function interpretRangeOfInteger($value): string
    {
        $half_size = strlen($value) / 2;
        $integer1 = $this->interpretInteger(substr($value, 0, $half_size));
        $integer2 = $this->interpretInteger(substr($value, $half_size, $half_size));

        return sprintf('%s-%s', $integer1, $integer2);
    }

    protected function interpretDateTime($date): string
    {
        $year = $this->interpretInteger(substr($date, 0, 2));
        $month = $this->interpretInteger(substr($date, 2, 1));
        $day = $this->interpretInteger(substr($date, 3, 1));
        $hour = $this->interpretInteger(substr($date, 4, 1));
        $minute = $this->interpretInteger(substr($date, 5, 1));
        $second = $this->interpretInteger(substr($date, 6, 1));
        $direction = substr($date, 8, 1);
        $hours_from_utc = $this->interpretInteger(substr($date, 9, 1));
        $minutes_from_utc = $this->interpretInteger(substr($date, 10, 1));

        $date = sprintf(
          '%s-%s-%s %s:%s:%s %s%s:%s',
          $year,
          $month,
          $day,
          $hour,
          $minute,
          $second,
          $direction,
          $hours_from_utc,
          $minutes_from_utc
        );

        $datetime = new DateTime($date);

        return $datetime->format('c');
    }

    /**
     * @param $attribute_name
     * @param $value
     *
     * @return array|mixed|string
     */
    protected function interpretEnum($attribute_name, $value): mixed
    {
        $value_parsed = $this->interpretInteger($value);

        switch ($attribute_name) {
            case 'client-type':
                $value = $this->interpretClientType($value_parsed);
                break;
            case 'document-state':
                $value = $this->interpretDocumentJobState($value_parsed, $value);
                if ($value_parsed > 0x09 || $value_parsed == 0x04) {
                    $value = sprintf('Unknown(IETF standards track "document-state" reserved): 0x%x', $value_parsed);
                }
                break;
            case 'finishings':
            case 'finishings-default':
            case 'finishings-ready':
            case 'finishings-supported':
                $value = $this->interpretFinishings($value_parsed);
                break;
            case 'job-state':
            case 'output-device-job-states':
                $value = $this->interpretDocumentJobState($value_parsed, $value);
                if ($value_parsed > 0x09) {
                    $value = sprintf('Unknown(IETF standards track "job-state" reserved): 0x%x', $value_parsed);
                }
                break;
            case 'operations-supported':
                $value = $this->interpretOperationsSupported($value_parsed, $value);
                break;
            case 'orientation-requested':
            case 'orientation-requested-supported':
            case 'orientation-requested-default':
            case 'image-orientation':
            case 'image-orientation-default':
            case 'image-orientation-supported':
            case 'input-orientation-requested':
            case 'input-orientation-requested-supported':
            case 'media-source-feed-orientation':
                $value = $this->interpretOrientation($value_parsed, $value);
                break;
            case 'power-state':
            case 'request-power-state':
            case 'start-power-state':
            case 'end-power-state':
                $value = $this->interpretPowerState($value_parsed);
                break;
            case 'print-quality':
            case 'print-quality-supported':
            case 'print-quality-default':
            case 'input-quality':
            case 'input-quality-supported':
                $value = $this->interpretPrintQuality($value_parsed, $value);
                break;
            case 'printer-state':
                $value = $this->interpretPrinterState($value_parsed, $value);
                break;
            case 'printer-type':
                $value = $this::interpretPrinterType($value);
                break;
            case 'resource-state':
                $value = $this->interpretResourceState($value_parsed, $value);
                break;
            case 'system-state':
                $value = $this->interpretSystemState($value_parsed, $value);
                break;
            case 'transmission-status':
                $value = $this->interpretTransmissionStatus($value_parsed, $value);
                break;
            default:
                break;
        }

        return $value;
    }

    protected function interpretPrinterType($value): array
    {
        $value_parsed = 0;

        for ($i = strlen($value); $i > 0; $i--) {
            $value_parsed += pow(256, ($i - 1)) * ord($value[strlen($value) - $i]);
        }

        $type[0] = $type[1] = $type[2] = $type[3] = $type[4] = $type[5] = '';
        $type[6] = $type[7] = $type[8] = $type[9] = $type[10] = '';
        $type[11] = $type[12] = $type[13] = $type[14] = $type[15] = '';
        $type[16] = $type[17] = $type[18] = $type[19] = '';

        if ($value_parsed % 2 == 1) {
            $type[0] = 'printer-class';
            $value_parsed -= 1;
        }
        if ($value_parsed % 4 == 2) {
            $type[1] = 'remote-destination';
            $value_parsed -= 2;
        }
        if ($value_parsed % 8 == 4) {
            $type[2] = 'print-black';
            $value_parsed -= 4;
        }
        if ($value_parsed % 16 == 8) {
            $type[3] = 'print-color';
            $value_parsed -= 8;
        }
        if ($value_parsed % 32 == 16) {
            $type[4] = 'hardware-print-on-both-sides';
            $value_parsed -= 16;
        }
        if ($value_parsed % 64 == 32) {
            $type[5] = 'hardware-staple-output';
            $value_parsed -= 32;
        }
        if ($value_parsed % 128 == 64) {
            $type[6] = 'hardware-fast-copies';
            $value_parsed -= 64;
        }
        if ($value_parsed % 256 == 128) {
            $type[7] = 'hardware-fast-copy-collation';
            $value_parsed -= 128;
        }
        if ($value_parsed % 512 == 256) {
            $type[8] = 'punch-output';
            $value_parsed -= 256;
        }
        if ($value_parsed % 1024 == 512) {
            $type[9] = 'cover-output';
            $value_parsed -= 512;
        }
        if ($value_parsed % 2048 == 1024) {
            $type[10] = 'bind-output';
            $value_parsed -= 1024;
        }
        if ($value_parsed % 4096 == 2048) {
            $type[11] = 'sort-output';
            $value_parsed -= 2048;
        }
        if ($value_parsed % 8192 == 4096) {
            $type[12] = 'handle-media-up-to-US-Legal-A4';
            $value_parsed -= 4096;
        }
        if ($value_parsed % 16384 == 8192) {
            $type[13] = 'handle-media-between-US-Legal-A4-and-ISO_C-A2';
            $value_parsed -= 8192;
        }
        if ($value_parsed % 32768 == 16384) {
            $type[14] = 'handle-media-larger-than-ISO_C-A2';
            $value_parsed -= 16384;
        }
        if ($value_parsed % 65536 == 32768) {
            $type[15] = 'handle-user-defined-media-sizes';
            $value_parsed -= 32768;
        }
        if ($value_parsed % 131072 == 65536) {
            $type[16] = 'implicit-server-generated-class';
            $value_parsed -= 65536;
        }
        if ($value_parsed % 262144 == 131072) {
            $type[17] = 'network-default-printer';
            $value_parsed -= 131072;
        }
        if ($value_parsed % 524288 == 262144) {
            $type[18] = 'fax-device';
            // $value_parsed -= 262144;
        }

        ksort($type);

        return $type;
    }

    protected function interpretFinishings($value_parsed): string
    {
        return match ($value_parsed) {
            3 => 'none',
            4 => 'staple',
            5 => 'punch',
            6 => 'cover',
            7 => 'bind',
            8 => 'saddle-stitch',
            9 => 'edge-stitch',
            20 => 'staple-top-left',
            21 => 'staple-bottom-left',
            22 => 'staple-top-right',
            23 => 'staple-bottom-right',
            24 => 'edge-stitch-left',
            25 => 'edge-stitch-top',
            26 => 'edge-stitch-right',
            27 => 'edge-stitch-bottom',
            28 => 'staple-dual-left',
            29 => 'staple-dual-top',
            30 => 'staple-dual-right',
            31 => 'staple-dual-bottom',
            32 => 'staple-triple-left',
            33 => 'staple-triple-top',
            34 => 'staple-triple-right',
            35 => 'staple-triple-bottom',
            50 => 'bind-left',
            51 => 'bind-top',
            52 => 'bind-right',
            53 => 'bind-bottom',
            60 => 'trim-after-pages',
            61 => 'trim-after-documents',
            62 => 'trim-after-copies',
            63 => 'trim-after-job',
            70 => 'punch-top-left',
            71 => 'punch-bottom-left',
            72 => 'punch-top-right',
            73 => 'punch-bottom-right',
            74 => 'punch-dual-left',
            75 => 'punch-dual-top',
            76 => 'punch-dual-right',
            77 => 'punch-dual-bottom',
            78 => 'punch-triple-left',
            79 => 'punch-triple-top',
            80 => 'punch-triple-right',
            81 => 'punch-triple-bottom',
            82 => 'punch-quad-left',
            83 => 'punch-quad-top',
            84 => 'punch-quad-right',
            85 => 'punch-quad-bottom',
            86 => 'punch-multiple-left',
            87 => 'punch-multiple-top',
            88 => 'punch-multiple-right',
            89 => 'punch-multiple-bottom',
            90 => 'fold-accordion',
            91 => 'fold-double-gate',
            92 => 'fold-gate',
            93 => 'fold-half',
            94 => 'fold-half-z',
            95 => 'fold-left-gate',
            96 => 'fold-letter',
            97 => 'fold-parallel',
            98 => 'fold-poster',
            99 => 'fold-right-gate',
            100 => 'fold-z',
            101 => 'fold-engineering-z',
            default => sprintf('Unknown(IETF standards track "finishing" reserved): 0x%x', $value_parsed),
        };
    }

    protected function interpretOperationsSupported($value_parsed, $value): string
    {
        if (in_array($value_parsed, [0x0000, 0x0001, 0x000F, 0x001D, 0x001F, 0x0021])) {
            switch ($value_parsed) {
                default:
                case 0x0000:
                case 0x0001:
                    $return_value = sprintf('Unknown(reserved) : %s', ord($value));
                    break;
                case 0x000F:
                    $return_value = 'Unknown(reserved for a future operation)';
                    break;
                case 0x001D:
                case 0x001F:
                case 0x0021:
                $return_value = sprintf('Unknown (reserved IETF "operations"): 0x%x', ord($value));
                    break;
            }
        } elseif ($value_parsed >= 0x4000 && $value_parsed <= 0x8FFF) {
            if (method_exists($this, '_getEnumVendorExtensions')) {
                $return_value = $this->_getEnumVendorExtensions($value_parsed);
            } else {
                $return_value = sprintf('Unknown(Vendor extension for "operations-supported"): 0x%x', $value_parsed);
            }
        } elseif ($value_parsed > 0x0067 && $value_parsed <= 0x3FFF) {
            $return_value = sprintf('Unknown(IETF standards track "operations-supported" reserved): 0x%x', $value_parsed);
        } elseif ($value_parsed > 0x8FFF) {
            $return_value = sprintf('Unknown "operations-supported" (should not exists): 0x%x', $value_parsed);
        } else {
            $return_value = Operations::getString($value_parsed);
        }

        return $return_value;
    }

    protected function interpretPowerState($value_parsed): string
    {
        return match ($value_parsed) {
            20 => 'on',
            21 => 'on-vendor1',
            22 => 'on-vendor2',
            23 => 'on-vendor3',
            24 => 'on-vendor4',
            25 => 'on-vendor5',
            30 => 'standby',
            31 => 'standby-vendor1',
            32 => 'standby-vendor2',
            33 => 'standby-vendor3',
            34 => 'standby-vendor4',
            35 => 'standby-vendor5',
            40 => 'suspend',
            41 => 'suspend-vendor1',
            42 => 'suspend-vendor2',
            43 => 'suspend-vendor3',
            44 => 'suspend-vendor4',
            45 => 'suspend-vendor5',
            50 => 'reset-soft',
            60 => 'off-hard',
            70 => 'hibernate',
            71 => 'hibernate-vendor1',
            72 => 'hibernate-vendor2',
            73 => 'hibernate-vendor3',
            74 => 'hibernate-vendor4',
            75 => 'hibernate-vendor5',
            80 => 'off-soft',
            81 => 'off-soft-vendor1',
            82 => 'off-soft-vendor2',
            83 => 'off-soft-vendor3',
            84 => 'off-soft-vendor4',
            85 => 'off-soft-vendor5',
            90 => 'reset-hard',
            100 => 'reset-mbr',
            110 => 'reset-nmi',
            120 => 'off-soft-graceful',
            130 => 'off-hard-graceful',
            140 => 'reset-mbr-graceful',
            150 => 'reset-soft-graceful',
            160 => 'reset-hard-graceful',
            170 => 'reset-init',
            180 => 'not-applicable',
            190 => 'no-change',
            default => sprintf('Unknown "power-state" (should not exists): 0x%x', $value_parsed),
        };
    }

    protected function interpretDocumentJobState($value_parsed, $value): string
    {
        switch ($value_parsed) {
            case 0x03:
                $value = 'pending';
                break;
            case 0x04:
                $value = 'pending-held';
                break;
            case 0x05:
                $value = 'processing';
                break;
            case 0x06:
                $value = 'processing-stopped';
                break;
            case 0x07:
                $value = 'canceled';
                break;
            case 0x08:
                $value = 'aborted';
                break;
            case 0x09:
                $value = 'completed';
                break;
        }

        return $value;
    }

    protected function interpretPrinterState($value_parsed, $value): string
    {
        switch ($value_parsed) {
            case 0x03:
                $value = 'idle';
                break;
            case 0x04:
                $value = 'processing';
                break;
            case 0x05:
                $value = 'stopped';
                break;
        }

        if ($value_parsed < 0x03 || $value_parsed > 0x05) {
            $value = sprintf('Unknown(IETF standards track "printer-state" reserved): 0x%x', $value_parsed);
        }

        return $value;
    }

    protected function interpretSystemState($value_parsed, $value): string
    {
        if ($value_parsed < 0x03 || $value_parsed > 0x05) {
            $value = sprintf('Unknown(IETF standards track "system-state" reserved): 0x%x', $value_parsed);
        } else {
            $value = $this->interpretPrinterState($value_parsed, $value);
        }

        return $value;
    }

    protected function interpretResourceState($value_parsed, $value): string
    {
        switch ($value_parsed) {
            case 0x03:
                $value = 'pending';
                break;
            case 0x04:
                $value = 'available';
                break;
            case 0x05:
                $value = 'installed';
                break;
            case 0x06:
                $value = 'canceled';
                break;
            case 0x07:
                $value = 'aborted';
                break;
        }

        if ($value_parsed < 0x03 || $value_parsed > 0x09) {
            $value = sprintf('Unknown(IETF standards track "resource-state" reserved): 0x%x', $value_parsed);
        }

        return $value;
    }

    protected function interpretTransmissionStatus($value_parsed, $value): string
    {
        switch ($value_parsed) {
            case 0x03:
                $value = 'pending';
                break;
            case 0x04:
                $value = 'pending-retry';
                break;
            case 0x05:
                $value = 'processing';
                break;
            case 0x07:
                $value = 'canceled';
                break;
            case 0x08:
                $value = 'aborted';
                break;
            case 0x09:
                $value = 'completed';
                break;
        }

        if ($value_parsed < 0x03 || $value_parsed > 0x09) {
            $value = sprintf('Unknown(IETF standards track "transmission-status" reserved): 0x%x', $value_parsed);
        }

        return $value;
    }

    protected function interpretPrintQuality($value_parsed, $value): string
    {
        switch ($value_parsed) {
            case 0x03:
                $value = 'draft';
                break;
            case 0x04:
                $value = 'normal';
                break;
            case 0x05:
                $value = 'high';
                break;
        }

        if ($value_parsed < 0x03 || $value_parsed > 0x05) {
            $value = sprintf('Unknown(IETF standards track "print-quality"): 0x%x', $value_parsed);
        }

        return $value;
    }

    protected function interpretOrientation($value_parsed, $value): string
    {
        switch ($value_parsed) {
            case 0x03:
                $value = 'portrait';
                break;
            case 0x04:
                $value = 'landscape';
                break;
            case 0x05:
                $value = 'reverse-landscape';
                break;
            case 0x06:
                $value = 'reverse-portrait';
                break;
        }

        if ($value_parsed < 0x03 || $value_parsed > 0x06) {
            $value = sprintf('Unknown(IETF standards track "orientation" reserved): 0x%x', $value_parsed);
        }

        return $value;
    }

    protected function interpretClientType($value_parsed): string
    {
        return match ($value_parsed) {
            0x03 => 'application',
            0x04 => 'operating-system',
            0x05 => 'driver',
            0x06 => 'other',
            default => sprintf('Unknown(IETF standards track "client-type" reserved): 0x%x', $value_parsed),
        };
    }
}
