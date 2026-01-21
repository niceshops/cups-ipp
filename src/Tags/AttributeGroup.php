<?php

namespace Smalot\Cups\Tags;

class AttributeGroup
{
    const int OPERATION_ATTRIBUTES_TAG = 0x01;
    const int JOB_ATTRIBUTES_TAG = 0x02;
    const int END_OF_ATTRIBUTES_TAG = 0x03;
    const int PRINTER_ATTRIBUTES_TAG = 0x04;
    const int UNSUPPORTED_ATTRIBUTES_TAG = 0x05;
    const int SUBSCRIPTION_ATTRIBUTES_TAG = 0x06;
    const int EVENT_NOTIFICATION_ATTRIBUTES_TAG = 0x07;
    const int RESOURCE_ATTRIBUTES_TAG = 0x08;
    const int DOCUMENT_ATTRIBUTES_TAG = 0x09;
    const int SYSTEM_ATTRIBUTES_TAG = 0x0A;

}
