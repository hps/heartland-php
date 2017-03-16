<?php

/**
 * Class Attachment
 */
class Attachment extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     * //@expectedException HpsGatewayException
     * //@expectedExceptionCode 10
     * //@expectedExceptionMessage Invalid card data
     * //\HpsAttachmentService::getAttachments
     */

    // GatewayTxnId
    const VALID_GATEWAY_ID = 1012040132;
    const NOT_VALID_GATEWAY_ID = 10120110941;


    // AttachmentDataId
    const VALID_ATTACHMENT_ID = 47032;
    const NOT_VALID_ATTACHMENT_ID = 469421;

    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode HpsExceptionCodes::UNKNOWN_GATEWAY_ERROR
     * @expectedExceptionMessage System error
     */

    public function getAttachmentsWithInvalidGatewayTxnIdShouldReturnHpsAttachment()    {
        $testConfig = new TestServicesConfig();
        $attachmentSvc = new HpsAttachmentService($testConfig::validMultiUseConfig());
        $attachmentSvc->getAttachments(self::NOT_VALID_GATEWAY_ID);
    }

    /**
     * @test
     */

    public function getAttachmentsWithGatewayTxnIdShouldReturnHpsAttachment()    {
        $testConfig = new TestServicesConfig();
        $attachmentSvc = new HpsAttachmentService($testConfig::validMultiUseConfig());
        $HpsAttachment = $attachmentSvc->getAttachments(self::VALID_GATEWAY_ID);
        $this->assertGreaterThan(0,$HpsAttachment->attachmentDataId,'AttachmentDataId is assigned by the gateway. Please check other parameters match the record.');
        $this->assertNotEmpty($HpsAttachment->attachmentData,'AttachmentData should be a base64 encoded string');
    }
    /**
     * @tests
     */

    public function getAttachmentsWithGatewayTxnIdAndReturnAttachmentTypesOnlyEqualsTrueShouldReturnHpsAttachmentWithBlankAttachmentData()    {
        $testConfig = new TestServicesConfig();
        $attachmentSvc = new HpsAttachmentService($testConfig::validMultiUseConfig());
        $HpsAttachment = $attachmentSvc->getAttachments(self::VALID_GATEWAY_ID,'all',true,self::VALID_ATTACHMENT_ID);
        $this->assertGreaterThan(0,$HpsAttachment->attachmentDataId,'AttachmentDataId is assigned by the gateway. Please check other parameters match the record.');
        $this->assertEmpty($HpsAttachment->attachmentData,'AttachmentData should be a blank string');
    }
    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode HpsExceptionCodes::GATEWAY_ERROR
     */

    public function getAttachmentsWithGatewayTxnIdAndAttachmentTypeAndAttachmentDataIdShouldReturnHpsGatewayException()    {
        $testConfig = new TestServicesConfig();
        $attachmentSvc = new HpsAttachmentService($testConfig::validMultiUseConfig());
        $attachmentSvc->getAttachments(self::VALID_GATEWAY_ID,HpsAttachmentType::CUSTOMER,false,self::VALID_ATTACHMENT_ID);
    }
    /**
     * @test
     */

    public function getAttachmentsWithGatewayTxnIdAndWrongAttachmentTypeShouldReturnEmptyHpsAttachment()    {
        $testConfig = new TestServicesConfig();
        $attachmentSvc = new HpsAttachmentService($testConfig::validMultiUseConfig());
        $HpsAttachment = $attachmentSvc->getAttachments(self::VALID_GATEWAY_ID,HpsAttachmentType::SIGNATURE);
        $this->assertEmpty($HpsAttachment->attachmentDataId,'Since a non matching AttachmentType was sent AttachmentDataId should have been blank');
        $this->assertEmpty($HpsAttachment->attachmentData,'Since a non matching AttachmentType was sent AttachmentData should be a blank');
        $this->assertGreaterThan(0,$HpsAttachment->transactionId);
        if (isset($HpsAttachment->_header)) {
            $this->assertEquals('Success', $HpsAttachment->_header->gatewayResponseMessage);
        }
    }

}
