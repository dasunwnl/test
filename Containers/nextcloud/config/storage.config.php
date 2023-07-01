<?php
$CONFIG = array (
'objectstore' => array (
        'class' => '\\OC\\Files\\ObjectStore\\S3',
        'arguments' => array (
                'bucket' => 'nextcloud',
                'region' => 'ap-mumbai-1',
                'hostname' => 'bmkltsly13vb.compat.objectstorage.ap-mumbai-1.oraclecloud.com',
                'key' => '9b816df9287bfb97f4ae7f00faa8fbe890d07992',
                'secret' => 'u7j5q0r/EQ4YG/xUR0MxvycJ1rGtj2nSKTQC0FD8leA=',
                'port' => 443,
                // required for some non-Amazon S3 implementations
                'use_path_style' => true,
        ),
),
);
