<?php

/*$argv[0]='';
$i=1;
while(isset($_GET['_'.$i])){
    $p=$_GET['_'.$i];
    $argv[$i]=$p;
    $i++;
};*/

$result='';
if($argv[1]=='1'){
    $version=$argv[2];
    $mch_code=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];
    
    for($i=6;$i<count($argv);$i++){
        $value=hls_decode_pub($argv[$i])['value'];
        
        $code_obj=hls_encode([
            'prefix'=>'',
            'version'=>$version,
            'value'=>(int)$value,
            'mch_code'=>$mch_code,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);
        
        $result.=$code_obj['code'].",".$value."\n";
    }
    echo substr($result,0,-1);
}
else if($argv[1]=='2'){
    $version=$argv[2];
    $mch_code_len=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];
    
    for($i=6;$i<count($argv);$i++){
        
        $code_obj=hls_decode([
            'code'=>$argv[$i],
            'version'=>$version,
            'mch_code_len'=>(int)$mch_code_len,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);
        
        $scalar=hls_encode_pub((int)$code_obj['value']);
        
        $result.=$scalar."\n";
    }
    echo substr($result,0,-1);
}
else if($argv[1]=='3'){
    $version=$argv[2];
    $mch_code=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];

    for($i=6;$i<count($argv);$i++){
        $code_obj=hls_encode([
            'prefix'=>'',
            'version'=>$version,
            'value'=>(int)$argv[$i],
            'mch_code'=>$mch_code,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);
        
        $result.=$code_obj['code']."\n";
    }
    echo substr($result,0,-1);
}
else if($argv[1]=='4'){
    for($i=2;$i<count($argv);$i++){

        $scalar=hls_encode_pub((int)$argv[$i]);

        $result.=$scalar."\n";
    }
    echo substr($result,0,-1);
}
else if($argv[1]=='5'){
    $version=$argv[2];
    $mch_code_len=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];
    
    for($i=6;$i<count($argv);$i++){
        
        $code_obj=hls_decode([
            'code'=>$argv[$i],
            'version'=>$version,
            'mch_code_len'=>(int)$mch_code_len,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);
        
        $scalar=hls_encode_pub((int)$code_obj['value']);
        
        $result.=$scalar.",".$code_obj['value']."\n";
    }
    echo substr($result,0,-1);
}
else if($argv[1]=='6'){
    $version=$argv[2];
    $mch_code=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];

    for($i=6;$i<count($argv);$i++){
        $value=hls_decode_pub($argv[$i])['value'];

        $code_obj=hls_encode([
            'prefix'=>'',
            'version'=>$version,
            'value'=>(int)$value,
            'mch_code'=>$mch_code,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);

        $result.=$code_obj['code']."\n";
    }
    echo substr($result,0,-1);
}
else if($argv[1]=='e'){
    $version=$argv[2];
    $mch_code=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];

    for($i=6;$i<count($argv);$i++){
        $code_obj=hls_encode([
            'prefix'=>'',
            'version'=>$version,
            'value'=>(int)$argv[$i],
            'mch_code'=>$mch_code,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);

        print_r($code_obj);
        //$result.=$code_obj['code']."\n";
    }
    //echo substr($result,0,-1);
}
else if($argv[1]=='d'){
    $version=$argv[2];
    $mch_code_len=$argv[3];
    $serial_len=$argv[4];
    $valid_len=$argv[5];

    for($i=6;$i<count($argv);$i++){
        
        $code_obj=hls_decode([
            'code'=>$argv[$i],
            'version'=>$version,
            'mch_code_len'=>(int)$mch_code_len,
            'serial_len'=>(int)$serial_len,
            'valid_len'=>(int)$valid_len
        ]);
        
        print_r($code_obj);
        //$result.=$code_obj['value']."\n";
    }
    //echo substr($result,0,-1);
}
else if($argv[1]=='pe'){
    $scalar=hls_encode_pub((int)$argv[2]);
    
    print_r($scalar);
}
else if($argv[1]=='pd'){
    $scalar=hls_decode_pub($argv[2]);

    print_r($scalar);
}