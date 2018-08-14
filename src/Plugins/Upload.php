<?php


class FileUpload
{
    public $storage;
    public $file;

    public function filePath($fileKey, $path)
    {
        $this->storage = new \Upload\Storage\FileSystem($path);
        $this->file = new \Upload\File($fileKey, $this->storage);
        return $this;
    }

    public function repairImageUpdate()
    {

        // Optionally you can rename the file on upload
        $new_filename = md5(uniqid("", true));
        $this->file->setName($new_filename);

        // Validate file upload
        // MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
        $this->file->addValidations([
            //You can also add multi mimetype validation
            new \Upload\Validation\Mimetype(['image/png', 'image/jpeg']),

            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new \Upload\Validation\Size('2M'),
        ]);

        // Access data about the file that has been uploaded
        $data = [
            'name' => $this->file->getNameWithExtension(),
            'extension' => $this->file->getExtension(),
            'mime' => $this->file->getMimetype(),
            'size' => $this->file->getSize(),
            'md5' => $this->file->getMd5(),
            'dimensions' => $this->file->getDimensions()
        ];

        // Try to upload file
        try {
            // Success!
            $this->file->upload();
            return ['status' => true, 'file_name' => $data['name']];
        } catch (\Exception $e) {
            // Fail!
            $errors = $this->file->getErrors();
            return ['status' => false, 'info' => $errors[0]];
        }
    }
}