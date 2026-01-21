<?php

namespace Smalot\Cups\Model;

use Smalot\Cups\CupsException;

class Operations
{
    const int PRINT_JOB = 0x0002;
    const int PRINT_URI = 0x0003;
    const int VALIDATE_JOB = 0x0004;
    const int CREATE_JOB = 0x0005;
    const int SEND_DOCUMENT = 0x0006;
    const int SEND_URI = 0x0007;
    const int CANCEL_JOB = 0x0008;
    const int GET_JOB_ATTRIBUTES = 0x0009;
    const int GET_JOBS = 0x000A;
    const int GET_PRINTER_ATTRIBUTES = 0x000B;
    const int HOLD_JOB = 0x000C;
    const int RELEASE_JOB = 0x000D;
    const int RESTART_JOB = 0x000E;
    const int PAUSE_PRINTER = 0x0010;
    const int RESUME_PRINTER = 0x0011;
    const int PURGE_JOBS = 0x0012;
    const int SET_PRINTER_ATTRIBUTES = 0x0013;
    const int SET_JOB_ATTRIBUTES = 0x0014;
    const int GET_PRINTER_SUPPORTED_VALUES = 0x0015;
    const int CREATE_PRINTER_SUBSCRIPTIONS = 0x0016;
    const int CREATE_JOB_SUBSCRIPTIONS = 0x0017;
    const int GET_SUBSCRIPTION_ATTRIBUTES = 0x0018;
    const int GET_SUBSCRIPTIONS = 0x0019;
    const int RENEW_SUBSCRIPTION = 0x001A;
    const int CANCEL_SUBSCRIPTION = 0x001B;
    const int GET_NOTIFICATION = 0x001C;
    const int GET_RESOURCE_ATTRIBUTES = 0x001E;
    const int GET_RESOURCES = 0x0020;
    const int ENABLE_PRINTER = 0x0022;
    const int DISABLE_PRINTER = 0x0023;
    const int PAUSE_PRINTER_AFTER_CURRENT_JOB = 0x0024;
    const int HOLD_NEW_JOBS = 0x0025;
    const int RELEASE_HELD_NEW_JOBS = 0x0026;
    const int DEACTIVATE_PRINTER = 0x0027;
    const int ACTIVATE_PRINTER = 0x0028;
    const int RESTART_PRINTER = 0x0029;
    const int SHUTDOWN_PRINTER = 0x002A;
    const int STARTUP_PRINTER = 0x002B;
    const int REPROCESS_JOB = 0x002C;
    const int CANCEL_CURRENT_JOB = 0x002D;
    const int SUSPEND_CURRENT_JOB = 0x002E;
    const int RESUME_JOB = 0x002F;
    const int PROMOTE_JOB = 0x0030;
    const int SCHEDULE_JOB_AFTER = 0x0031;
    const int CANCEL_DOCUMENT = 0x0033;
    const int GET_DOCUMENT_ATTRIBUTES = 0x0034;
    const int GET_DOCUMENTS = 0x0035;
    const int DELETE_DOCUMENT = 0x0036;
    const int SET_DOCUMENT_ATTRIBUTES = 0x0037;
    const int CANCEL_JOBS = 0x0038;
    const int CANCEL_MY_JOBS = 0x0039;
    const int RESUBMIT_JOB = 0x003A;
    const int CLOSE_JOB = 0x003B;
    const int IDENTIFY_PRINTER = 0x003C;
    const int VALIDATE_DOCUMENT = 0x003D;
    const int ADD_DOCUMENT_IMAGES = 0x003E;
    const int ACKNOWLEDGE_DOCUMENT = 0x003F;
    const int ACKNOWLEDGE_IDENTIFY_PRINTER = 0x0040;
    const int ACKNOWLEDGE_JOB = 0x0041;
    const int FETCH_DOCUMENT = 0x0042;
    const int FETCH_JOB = 0x0043;
    const int GET_OUTPUT_DEVICE_ATTRIBUTES = 0x0044;
    const int UPDATE_ACTIVE_JOBS = 0x0045;
    const int DEREGISTER_OUTPUT_DEVICE = 0x0046;
    const int UPDATE_DOCUMENT_STATUS = 0x0047;
    const int UPDATE_JOB_STATUS = 0x0048;
    const int UPDATE_OUTPUT_DEVICE_ATTRIBUTES = 0x0049;
    const int GET_NEXT_DOCUMENT_DATA = 0x004A;
    const int ALLOCATE_PRINTER_RESOURCES = 0x004B;
    const int CREATE_PRINTER = 0x004C;
    const int DEALLOCATE_PRINTER_RESOURCES = 0x004D;
    const int DELETE_PRINTER = 0x004E;
    const int GET_PRINTERS = 0x004F;
    const int SHUTDOWN_ONE_PRINTER = 0x0050;
    const int STARTUP_ONE_PRINTER = 0x0051;
    const int CANCEL_RESOURCE = 0x0052;
    const int CREATE_RESOURCE = 0x0053;
    const int INSTALL_RESOURCE = 0x0054;
    const int SEND_RESOURCE_DATA = 0x0055;
    const int SET_RESOURCE_ATTRIBUTES = 0x0056;
    const int CREATE_RESOURCE_SUBSCRIPTIONS = 0x0057;
    const int CREATE_SYSTEM_SUBSCRIPTIONS = 0x0058;
    const int DISABLE_ALL_PRINTERS = 0x0059;
    const int ENABLE_ALL_PRINTERS = 0x005A;
    const int GET_SYSTEM_ATTRIBUTES = 0x005B;
    const int GET_SYSTEM_SUPPORTED_VALUES = 0x005C;
    const int PAUSE_ALL_PRINTERS = 0x005D;
    const int PAUSE_ALL_PRINTERS_AFTER_CURRENT_JOB = 0x005E;
    const int REGISTER_OUTPUT_DEVICE = 0x005F;
    const int RESTART_SYSTEM = 0x0060;
    const int RESUME_ALL_PRINTERS = 0x0061;
    const int SET_SYSTEM_ATTRIBUTES = 0x0062;
    const int SHUTDOWN_ALL_PRINTERS = 0x0063;
    const int STARTUP_ALL_PRINTERS = 0x0064;
    const int GET_PRINTER_RESOURCES = 0x0065;
    const int GET_USER_PRINTER_ATTRIBUTES = 0x0066;
    const int RESTART_ONE_PRINTER = 0x0067;
    const int CUPS_GET_DEFAULT = 0x4001;
    const int CUPS_GET_PRINTERS = 0x4002;
    const int CUPS_ADD_MODIFY_PRINTER = 0x4003;
    const int CUPS_DELETE_PRINTER = 0x4004;
    const int CUPS_GET_CLASSES = 0x4005;
    const int CUPS_ADD_MODIFY_CLASS = 0x4006;
    const int CUPS_DELETE_CLASS = 0x4007;
    const int CUPS_ACCEPT_JOBS = 0x4008;
    const int CUPS_REJECT_JOBS = 0x4009;
    const int CUPS_SET_DEFAULT = 0x400A;
    const int CUPS_GET_DEVICES = 0x400B;
    const int CUPS_GET_PPDS = 0x400C;
    const int CUPS_MOVE_JOB = 0x400D;
    const int CUPS_AUTHENTICATE_JOB = 0x400E;
    const int CUPS_GET_PPD = 0x400F;
    const int CUPS_GET_DOCUMENT = 0x4027;
    const int CUPS_CREATE_LOCAL_PRINTER = 0x4028;

    /**
     * Convert a command constant into a usable byte string
     *
     * @param $const
     *
     * @return string
     * @throws CupsException
     */
    public static function getOperationID($const): string
    {
        $parts = str_split(str_pad(dechex($const), 4,'0', STR_PAD_LEFT), 2);
        if (count($parts) === 2) {
            return chr(hexdec('0x'.$parts[0])) . chr(hexdec('0x'.$parts[1]));
        }

        throw new CupsException("Invalid Operation");
    }

    /**
     * Convert an integer representation of a supported operation into a string
     *
     * @param $identifier
     *
     * @return false|string
     */
    public static function getString($identifier): false|string
    {
        switch ($identifier) {
            case self::PRINT_JOB:
                $value = 'Print-Job';
                break;
            case self::PRINT_URI:
                $value = 'Print-URI';
                break;
            case self::VALIDATE_JOB:
                $value = 'Validate-Job';
                break;
            case self::CREATE_JOB:
                $value = 'Create-Job';
                break;
            case self::SEND_DOCUMENT:
                $value = 'Send-Document';
                break;
            case self::SEND_URI:
                $value = 'Send-URI';
                break;
            case self::CANCEL_JOB:
                $value = 'Cancel-Job';
                break;
            case self::GET_JOB_ATTRIBUTES:
                $value = 'Get-Job-Attributes';
                break;
            case self::GET_JOBS:
                $value = 'Get-Jobs';
                break;
            case self::GET_PRINTER_ATTRIBUTES:
                $value = 'Get-Printer-Attributes';
                break;
            case self::HOLD_JOB:
                $value = 'Hold-Job';
                break;
            case self::RELEASE_JOB:
                $value = 'Release-Job';
                break;
            case self::RESTART_JOB:
                $value = 'Restart-Job';
                break;
            case self::PAUSE_PRINTER:
                $value = 'Pause-Printer';
                break;
            case self::RESUME_PRINTER:
                $value = 'Resume-Printer';
                break;
            case self::PURGE_JOBS:
                $value = 'Purge-Jobs';
                break;
            case self::SET_PRINTER_ATTRIBUTES:
                $value = 'Set-Printer-Attributes'; // RFC3380
                break;
            case self::SET_JOB_ATTRIBUTES:
                $value = 'Set-Job-Attributes'; // RFC3380
                break;
            case self::GET_PRINTER_SUPPORTED_VALUES:
                $value = 'Get-Printer-Supported-Values'; // RFC3380
                break;
            case self::CREATE_PRINTER_SUBSCRIPTIONS:
                $value = 'Create-Printer-Subscriptions';
                break;
            case self::CREATE_JOB_SUBSCRIPTIONS:
                $value = 'Create-Job-Subscriptions';
                break;
            case self::GET_SUBSCRIPTION_ATTRIBUTES:
                $value = 'Get-Subscription-Attributes';
                break;
            case self::GET_SUBSCRIPTIONS:
                $value = 'Get-Subscriptions';
                break;
            case self::RENEW_SUBSCRIPTION:
                $value = 'Renew-Subscription';
                break;
            case self::CANCEL_SUBSCRIPTION:
                $value = 'Cancel-Subscription';
                break;
            case self::GET_NOTIFICATION:
                $value = 'Get-Notifications';
                break;
            case self::GET_RESOURCE_ATTRIBUTES:
                $value = 'Get-Resource-Attributes';
                break;
            case self::GET_RESOURCES:
                $value = 'Get-Resources';
                break;
            case self::ENABLE_PRINTER:
                $value = 'Enable-Printer';
                break;
            case self::DISABLE_PRINTER:
                $value = 'Disable-Printer';
                break;
            case self::PAUSE_PRINTER_AFTER_CURRENT_JOB:
                $value = 'Pause-Printer-After-Current-Job';
                break;
            case self::HOLD_NEW_JOBS:
                $value = 'Hold-New-Jobs';
                break;
            case self::RELEASE_HELD_NEW_JOBS:
                $value = 'Release-Held-New-Jobs';
                break;
            case self::DEACTIVATE_PRINTER:
                $value = 'Deactivate-Printer';
                break;
            case self::ACTIVATE_PRINTER:
                $value = 'Activate-Printer';
                break;
            case self::RESTART_PRINTER:
                $value = 'Restart-Printer';
                break;
            case self::SHUTDOWN_PRINTER:
                $value = 'Shutdown-Printer';
                break;
            case self::STARTUP_PRINTER:
                $value = 'Startup-Printer';
                break;
            case self::REPROCESS_JOB:
                $value = 'Reprocess-Job';
                break;
            case self::CANCEL_CURRENT_JOB:
                $value = 'Cancel-Current-Job';
                break;
            case self::SUSPEND_CURRENT_JOB:
                $value = 'Suspend-Current-Job';
                break;
            case self::RESUME_JOB:
                $value = 'Resume-Job';
                break;
            case self::PROMOTE_JOB:
                $value = 'Promote-Job';
                break;
            case self::SCHEDULE_JOB_AFTER:
                $value = 'Schedule-Job-After';
                break;
            case self::CANCEL_DOCUMENT:
                $value = 'Cancel-Document';
                break;
            case self::GET_DOCUMENT_ATTRIBUTES:
                $value = 'Get-Document-Attributes';
                break;
            case self::GET_DOCUMENTS:
                $value = 'Get-Documents';
                break;
            case self::DELETE_DOCUMENT:
                $value = 'Delete-Document';
                break;
            case self::SET_DOCUMENT_ATTRIBUTES:
                $value = 'Set-Document-Attributes';
                break;
            case self::CANCEL_JOBS:
                $value = 'Cancel-Jobs';
                break;
            case self::CANCEL_MY_JOBS:
                $value = 'Cancel-My-Jobs';
                break;
            case self::RESUBMIT_JOB:
                $value = 'Resubmit-Job';
                break;
            case self::CLOSE_JOB:
                $value = 'Close-Job';
                break;
            case self::IDENTIFY_PRINTER:
                $value = 'Identify-Printer';
                break;
            case self::VALIDATE_DOCUMENT:
                $value = 'Validate-Document';
                break;
            case self::ADD_DOCUMENT_IMAGES:
                $value = 'Add-Document-Images';
                break;
            case self::ACKNOWLEDGE_DOCUMENT:
                $value = 'Acknowledge-Document';
                break;
            case self::ACKNOWLEDGE_IDENTIFY_PRINTER:
                $value = 'Acknowledge-Identify-Printer';
                break;
            case self::ACKNOWLEDGE_JOB:
                $value = 'Acknowledge-Job';
                break;
            case self::FETCH_DOCUMENT:
                $value = 'Fetch-Document';
                break;
            case self::FETCH_JOB:
                $value = 'Fetch-Job';
                break;
            case self::GET_OUTPUT_DEVICE_ATTRIBUTES:
                $value = 'Get-Output-Device-Attributes';
                break;
            case self::UPDATE_ACTIVE_JOBS:
                $value = 'Update-Active-Jobs';
                break;
            case self::DEREGISTER_OUTPUT_DEVICE:
                $value = 'Deregister-Output-Device';
                break;
            case self::UPDATE_DOCUMENT_STATUS:
                $value = 'Update-Document-Status';
                break;
            case self::UPDATE_JOB_STATUS:
                $value = 'Update-Job-Status';
                break;
            case self::UPDATE_OUTPUT_DEVICE_ATTRIBUTES:
                $value = 'Update-Output-Device-Attributes';
                break;
            case self::GET_NEXT_DOCUMENT_DATA:
                $value = 'Get-Next-Document-Data';
                break;
            case self::ALLOCATE_PRINTER_RESOURCES:
                $value = 'Allocate-Printer-Resources';
                break;
            case self::CREATE_PRINTER:
                $value = 'Create-Printer';
                break;
            case self::DEALLOCATE_PRINTER_RESOURCES:
                $value = 'Deallocate-Printer-Resources';
                break;
            case self::DELETE_PRINTER:
                $value = 'Delete-Printer';
                break;
            case self::GET_PRINTERS:
                $value = 'Get-Printers';
                break;
            case self::SHUTDOWN_ONE_PRINTER:
                $value = 'Shutdown-One-Printer';
                break;
            case self::STARTUP_ONE_PRINTER:
                $value = 'Startup-One-Printer';
                break;
            case self::CANCEL_RESOURCE:
                $value = 'Cancel-Resource';
                break;
            case self::CREATE_RESOURCE:
                $value = 'Create-Resource';
                break;
            case self::INSTALL_RESOURCE:
                $value = 'Install-Resource';
                break;
            case self::SEND_RESOURCE_DATA:
                $value = 'Send-Resource-Data';
                break;
            case self::SET_RESOURCE_ATTRIBUTES:
                $value = 'Set-Resource-Attributes';
                break;
            case self::CREATE_RESOURCE_SUBSCRIPTIONS:
                $value = 'Create-Resource-Subscriptions';
                break;
            case self::CREATE_SYSTEM_SUBSCRIPTIONS:
                $value = 'Create-System-Subscriptions';
                break;
            case self::DISABLE_ALL_PRINTERS:
                $value = 'Disable-All-Printers';
                break;
            case self::ENABLE_ALL_PRINTERS:
                $value = 'Enable-All-Printers';
                break;
            case self::GET_SYSTEM_ATTRIBUTES:
                $value = 'Get-System-Attributes';
                break;
            case self::GET_SYSTEM_SUPPORTED_VALUES:
                $value = 'Get-System-Supported-Values';
                break;
            case self::PAUSE_ALL_PRINTERS:
                $value = 'Pause-All-Printers';
                break;
            case self::PAUSE_ALL_PRINTERS_AFTER_CURRENT_JOB:
                $value = 'Pause-All-Printers-After-Current-Job';
                break;
            case self::REGISTER_OUTPUT_DEVICE:
                $value = 'Register-Output-Device';
                break;
            case self::RESTART_SYSTEM:
                $value = 'Restart-System';
                break;
            case self::RESUME_ALL_PRINTERS:
                $value = 'Resume-All-Printers';
                break;
            case self::SET_SYSTEM_ATTRIBUTES:
                $value = 'Set-System-Attributes';
                break;
            case self::SHUTDOWN_ALL_PRINTERS:
                $value = 'Shutdown-All-Printers';
                break;
            case self::STARTUP_ALL_PRINTERS:
                $value = 'Startup-All-Printers';
                break;
            case self::GET_PRINTER_RESOURCES:
                $value = 'Get-Printer-Resources';
                break;
            case self::GET_USER_PRINTER_ATTRIBUTES:
                $value = 'Get-User-Printer-Attributes';
                break;
            case self::RESTART_ONE_PRINTER:
                $value = 'Restart-One-Printer';
                break;
            default:
                return false;
        }

        return $value;
    }
}
