<?php
/**
 * UploadedFile class file.
 * @author Christoffer Lindqvist <christoffer.lindqvist@nordsoftware.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-filemanager.resources
 */

/**
 * File resource that represents an uploaded file.
 */
class UploadedFile extends FileResource
{
    /**
     * @var CUploadedFile the uploaded file instance.
     */
    protected $file;

    /**
     * Creates a new file resource.
     * @param CUploadedFile $file the uploaded file instance.
     */
    public function __construct(CUploadedFile $file)
    {
        $this->file = $file;
        $this->init();
    }

    /**
     * Checks if there were any errors during file upload.
     * @throws CException
     */
    protected function init()
    {
        if ($this->file->hasError) {
            throw new CException(sprintf(
                'File could not be uploaded: %s',
                $this->getUploadError($this->file->getError())
            ));
        }
    }

    /**
     * Returns the upload error message for the given error code.
     * @param int $code the error code.
     * @return string the message.
     */
    protected function getUploadError($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File too large.';
            case UPLOAD_ERR_PARTIAL:
                return 'File upload was not completed.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Temporary folder missing.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            case UPLOAD_ERR_OK:
            default:
                return 'OK';
        }
    }

    /**
     * Returns the file extension.
     * @return string the extension.
     */
    public function getExtension()
    {
        return $this->file->getExtensionName();
    }

    /**
     * Returns the file name.
     * @return string the filename.
     */
    public function getName()
    {
        return $this->file->getName();
    }

    /**
     * Returns the file mime type.
     * @return string the file mime type.
     */
    public function getMimeType()
    {
        return CFileHelper::getMimeType($this->file->getTempName());
    }

    /**
     * Returns the files size in bytes.
     * @return int the file size.
     */
    public function getSize()
    {
        return $this->file->getSize();
    }

    /**
     * Saves the file to the specified path.
     * @param string $path the path where to save the file.
     * @return bool if the file was successfully saved.
     */
    public function saveAs($path)
    {
        return $this->file->saveAs($path);
    }
}