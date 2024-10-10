<?php

namespace App\Enums;

enum FileUploadStatus : string
{
    case Pending = 'pending';
    case Uploading = 'uploading';
    case Uploaded = 'uploaded';
}
