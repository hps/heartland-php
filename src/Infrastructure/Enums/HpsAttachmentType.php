<?php

/**
 * Created by PhpStorm.
 * User: charles.simmons
 * Date: 7/15/2016
 * Time: 3:23 PM
 */
class HpsAttachmentType
{
    const SIGNATURE = 'SIGNATURE_IMAGE';
    const RECEIPT   = 'RECEIPT_IMAGE';
    const CUSTOMER  = 'CUSTOMER_IMAGE';
    const PRODUCT   = 'PRODUCT_IMAGE';
    const DOCUMENT  = 'DOCUMENT';


    // validation regex for AttachmentFormat
    const VALID_ATTACHMENT_FORMAT = '/^(PNG|JPG|TIF|BMP|PDF|DOCX|DOC|TXT|XLS|XLSX)$/';
    // validation regex for AttachmentType
    const VALID_ATTACHMENT_TYPE = '/^(SIGNATURE_IMAGE|RECEIPT_IMAGE|CUSTOMER_IMAGE|PRODUCT_IMAGE|DOCUMENT)$/';
}