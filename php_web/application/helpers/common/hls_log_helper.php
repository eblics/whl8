<?php
    //test 日志记录
    function hls_log_write($log_content)
    {
        $max_size = 10000;
        $log_filename = "log.txt";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
    }

