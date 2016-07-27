<?php
/**
 * Class HpsAttachment
 * implements AttachmentRspDataType Complex Type
 * @link  https://posgateway.cert.secureexchange.net/Gateway/PorticoSOAPSchema/build/Default/webframe.html#Portico%20Schema_xsd~c-PosGetAttachmentsRspType~e-Details.html
 * @package  PHP-SDK/src/Entities/Attachment/HpsAttachment.php
 * show off @property
 *
 * @property string    $attachmentType
 * @property string    $attachmentData
 * @property string    $attachmentFormat
 * @property int       $height
 * @property int       $width
 * @property string    $attachmentName
 * @property int       $attachmentDataId
 */
class HpsAttachment extends HpsTransaction
{
    // https://posgateway.cert.secureexchange.net/Gateway/PorticoSOAPSchema/build/Default/webframe.html#Portico%20Schema_xsd~s-attachmentTypeType.html
    /**
     * @var string
     */
    private $attachmentType = ''; // string
    /**
     * @var string
     */
    private $attachmentData = ''; // string Base64 encoded attachment data
    /*https://posgateway.cert.secureexchange.net/Gateway/PorticoSOAPSchema/build/Default/webframe.html#Portico%20Schema_xsd~s-attachmentFormatType.html*/
    /**
     * @var string
     */
    private $attachmentFormat = ''; // string
    /**
     * @var int
     */
    private $height = 0; // int
    /**
     * @var int
     */
    private $width = 0; // int
    /*A merchant-assigned name for the associated attachment    */
    /**
     * @var string
     */
    private $attachmentName = ''; // string
    /*Gateway-generated attachment identifier    */
    /**
     * @var int
     */
    private $attachmentDataId = 0; // int

    /**
     * @param \SimpleXMLElement $rsp
     * @param null $txnType
     * @param string $returnType
     *
     * @return HpsAttachment
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsAttachment')
    {
        $attResponse = $rsp->Transaction->$txnType->Details;
        $transaction = parent::fromDict($rsp, $txnType, $returnType);
        foreach (get_object_vars($attResponse) as $key => $prop) {
            if (property_exists($transaction, lcfirst($key))) {
                $transaction->__set(lcfirst($key), $prop);
            }
        }
        return $transaction;
    }

    /**
     * @param string $name
     * @param float|string|int $value Never a Bool
     * @throws HpsArgumentException
     */
    public function __set($name, $value)
    {
        if (!$value instanceof stdClass) {
            if (!property_exists(__CLASS__, $name)) {
                $value = null;
            }
            switch ($name) {
                case 'height':
                case 'width':
                case 'attachmentDataId':
                    $validator = FILTER_SANITIZE_NUMBER_INT;
                    break;
                default:
                    $validator = FILTER_SANITIZE_STRING;
            }
            $value = filter_var($value, $validator);

            if ($name === 'attachmentFormat' && preg_match(HpsAttachmentType::VALID_ATTACHMENT_FORMAT, $value) !== 1) {
                throw new \HpsArgumentException('attachmentFormat not a valid enum', 1);
            }

            if ($value === false) {
                throw new \HpsArgumentException('invalid parameter for {' . __CLASS__ . '}', 1);
            } //
            $this->{$name} = $value;
        }

    }

    /**
     * @param string $name
     * @return float|string|int null
     */
    public function __get($name)
    {
        $value = null;
        if (property_exists(__CLASS__, $name)) {
            $value = $this->{$name};
        }
        return $value;
    }
}