#ifdef HAVE_CONFIG_H
    #include "config.h"
#endif
#include "php.h"
#include "php_ini.h"
#include "php_hls.h"
#include "ext/standard/info.h"
#include "codec.h"
#include "randomchar62.h"
#include "randomchar36.h"
#define PUB_CODE_DE_LEN  7
#define PUB_CODE_EN_LEN  9
/*#define int64 long long*/
void *codec;

char char62set[62]={'X','Q','W','R','y','P','l','I','Y','0','L','e','9','a','f',
    'U','C','K','m','n','V','Z','o','D','d','M','b','F','c','i','v','u','J','5','j',
    'x','8','3','N','w','B','h','A','z','r','E','H','t','k','s','S','2','1','g',
    '7','q','O','p','G','T','4','6'};
char char62map[256];
char char32set[32]={'A','B','C','D','E','F','G','H','0','1','2','3','4','5','6','7','8','9','J','K','L','M','N','P','R','T','U','V','W','X','Y','Z'};
char char32map[256];
int c62toc36len[16] = { 0,2,3,4,5,6,7,9,10,11,12,13,14,15,17,18 };
int c36toc62len[19] = { 0,0,1,2,3,4,5,6,0,7,8,9,10,11,12,13,0,14,15 };

int indexof(char *str, char chr)
{
    int i = 0;
    while (str[i] != '\0')
    {
        if (str[i] == chr)
            return i;
        i++;
    }
    return -1;
}

void disorder_asc_by_random(char code[], char * randomchars[], int charslen, char charset[]) {
    uint64_t id = 0;
    uint64_t times = 1;
    uint64_t number = 0;
    uint64_t index = 0;
    uint64_t len = strlen(code);
    uint64_t i, num;
    for (i = 0; i < len; i++) {
        num = indexof(charset, code[len - i - 1]);
        if (num == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }

        index = (id + number) % charslen;
        code[len - i - 1] = randomchars[index][num];
        id += times;
        number += num * times;
        times *= strlen(charset);

        number %= charslen;
        times %= charslen;
    }
}

void disorder_desc_by_random(char code[], char * randomchars[], int charslen, char charset[]) {
    uint64_t id = 0;
    uint64_t times = 1;
    uint64_t number = 0;
    uint64_t index = 0;
    uint64_t len = strlen(code);
    uint64_t i, num;
    for (i = 0; i < len; i++) {
        num = indexof(charset, code[i]);
        if (num == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }

        index = (id + number) % charslen;
        code[i] = randomchars[index][num];
        id += times;
        number += num * times;
        times *= strlen(charset);

        number %= charslen;
        times %= charslen;
    }
}

void disorder_by_random(char code[], char * randomchars[], int charslen, char charset[]) {
    disorder_asc_by_random(code, randomchars, charslen, charset);
    disorder_desc_by_random(code, randomchars, charslen, charset);
    disorder_asc_by_random(code, randomchars, charslen, charset);
    disorder_desc_by_random(code, randomchars, charslen, charset);
    disorder_asc_by_random(code, randomchars, charslen, charset);
    disorder_desc_by_random(code, randomchars, charslen, charset);
}

void order_asc_by_random(char code[], char * randomchars[], int charslen, char charset[]) {
    uint64_t id = 0;
    uint64_t times = 1;
    uint64_t number = 0;
    uint64_t index = 0;
    uint64_t len = strlen(code);
    uint64_t i, num;
    for (i = 0; i < len; i++) {
        index = (id + number) % charslen;

        num = indexof(randomchars[index], code[len - i - 1]);
        if (num == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }
        code[len - i - 1] = charset[num];
        id += times;
        number += num * times;
        times *= strlen(charset);

        number %= charslen;
        times %= charslen;
    }
}

void order_desc_by_random(char code[], char * randomchars[], int charslen, char charset[]) {
    uint64_t id = 0;
    uint64_t times = 1;
    uint64_t number = 0;
    uint64_t index = 0;
    uint64_t len = strlen(code);
    uint64_t i, num;
    for (i = 0; i < len; i++) {
        index = (id + number) % charslen;

        num = indexof(randomchars[index], code[i]);
        if (num == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }
        code[i] = charset[num];
        id += times;
        number += num * times;
        times *= strlen(charset);

        number %= charslen;
        times %= charslen;
    }
}

void order_by_random(char code[], char * randomchars[], int charslen, char charset[]) {
    order_desc_by_random(code, randomchars, charslen, charset);
    order_asc_by_random(code, randomchars, charslen, charset);
    order_desc_by_random(code, randomchars, charslen, charset);
    order_asc_by_random(code, randomchars, charslen, charset);
    order_desc_by_random(code, randomchars, charslen, charset);
    order_asc_by_random(code, randomchars, charslen, charset);
}

void c62toc36(char code[]) {
    uint64_t times = 1;
    uint64_t num = 0;
    uint64_t len = strlen(code);
    char* char62 = randomchar62[0];
    char* char36 = randomchar36[0];
    uint64_t i, temp, index;
    for (i = 0; i < len; i++) {
        index = indexof(char62, code[len - i - 1]);
        if (index == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }
        num += index * times;
        times *= strlen(char62);
    }
    memset(code, 0, len);
    times = 1;

    len = c62toc36len[len];
    for (i = 0; i < len; i++) {
        temp = num >= times ? (num / times) : 0;
        code[len - i - 1] = char36[temp % strlen(char36)];
        times *= strlen(char36);
    }
}

void c36toc62(char code[]) {
    uint64_t times = 1;
    uint64_t num = 0;
    uint64_t len = strlen(code);
    char* char62 = randomchar62[0];
    char* char36 = randomchar36[0];
    uint64_t i, temp, index;
    for (i = 0; i < len; i++) {
        index = indexof(char36, code[len - i - 1]);
        if (index == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }
        num += index * times;

        if (num == -1) {
            memset(code, 0, len);
            code[0] = '?';
            return;
        }
        times *= strlen(char36);
    }
    memset(code, 0, len);
    times = 1;

    len = c36toc62len[len];
    if (len == 0)
        code[0] = '?';
    for (i = 0; i < len; i++) {
        temp = num >= times ? (num / times) : 0;
        code[len - i - 1] = char62[temp % strlen(char62)];
        times *= strlen(char62);

        if (i == len - 1 && temp >= strlen(char62)) {
            memset(code, 0, len);
            code[0] = '?';
        }
    }
}

void lc36toc62(char code[]) {
    char part1[25], part2[25];
    char* p2;
    memset(part1, 0, sizeof(part1));
    memset(part2, 0, sizeof(part2));
    memcpy(part1, code, 12);
    p2 = code;
    p2 += 12;
    memcpy(part2, p2, 12);
    c36toc62(part1);
    if (strlen(code) > 12) {
        c36toc62(part2);
    }
    memset(code, 0, strlen(code));
    if (part1[0] == '?' || part2[0] == '?') {
        code[0] = '?';
    }
    else {
        sprintf(code, "%s%s", part1, part2);
    }
}

void lc62toc36(char code[]) {
    char part1[20], part2[20];
    char* p2;
    memset(part1, 0, sizeof(part1));
    memset(part2, 0, sizeof(part2));
    memcpy(part1, code, 10);
    p2 = code;
    p2 += 10;
    memcpy(part2, p2, 10);
    c62toc36(part1);
    if (strlen(code) > 10) {
        c62toc36(part2);
    }
    memset(code, 0, strlen(code));
    if (part1[0] == '?' || part2[0] == '?') {
        code[0] = '?';
    }
    else {
        sprintf(code, "%s%s", part1, part2);
    }
}

char* itoc62(uint64_t num,char str[],int size){
    uint64_t i,j;
    uint64_t rem;
    for(rem=0,j=1;num>=0&&j<=size;j++){
        rem=num%62;
        num/=62;
        str[size-j]=char62set[rem];
    }
    return str;
}

char* itoc32(uint64_t num,char str[],int size){
    uint64_t  i,j;
    uint64_t  rem;

    rem=0;
    for(j=1;num>=0&&j<=size;j++){
        rem=num%32;
        num/=32;
        str[size-j]=char32set[rem];
    }
    return str;
}

uint64_t c62toi(char* str){
    uint64_t i,len,num;
    num=0;
    len=strlen(str);

    for(i=0;i<len;i++){
        num+=char62map[str[i]]*pow(62,len-i-1);
    }
    return num;
}
uint64_t c32toi(char* str){
    uint64_t i,len,num;
    num=0;
    len=strlen(str);
    for(i=0;i<len;i++){
        num+=char32map[str[i]]*pow(32,len-i-1);
    }
    return num;
}

uint64_t dek_hash(char* str)
{
    uint64_t i,len,hash;
    len=strlen(str);
    hash = len;
    for(i = 0; i <len; i++)
    {
        hash = ((hash << 5) ^ (hash >> 27)) ^ str[i];
    }
    return hash;
}

void get62vc(char* str,char *code,int len){
    uint64_t hash;
    hash=dek_hash(str);
    itoc62(hash,code,len);
}

uint64_t strsum(char* str){
    uint64_t i,sum;
    sum=0;
    for(i=0;i<strlen(str);i++){
        sum+=str[i];
    }
    return sum;
}

char* disorder(char str[],int size){
    uint64_t i,l,k,c,seed,len,sum;
    seed=17;
    len=strlen(str);
    sum=seed+strsum(str);
    l=sum%len;
    for(i=0;i<l/2;i++){
        k=l-i;
        c=str[i];
        str[i]=str[k];
        str[k]=c;
    }
    for(i=l;i<(len-l)/2+l;i++){
        k=len+l-i-1;
        c=str[i];
        str[i]=str[k];
        str[k]=c;
    }
    return str;
}

char* order(char* str,int size){
    uint64_t i,l,k,c,seed,len,sum;
    seed=17;
    len=strlen(str);
    sum=seed+strsum(str);
    l=sum%len;
    for(i=l;i<(len-l)/2+l;i++){
        k=len+l-i-1;
        c=str[i];
        str[i]=str[k];
        str[k]=c;
    }
    for(i=0;i<l/2;i++){
        k=l-i;
        c=str[i];
        str[i]=str[k];
        str[k]=c;
    }
    return str;
}

void encode(char *version,char* mch_code,uint64_t serial_len,uint64_t valid_len,uint64_t value,char *code,char *pub_code){
    char format[36];
    char serial_code[serial_len+1];
    char vc[valid_len+1];
    //char code_sc[serial_len+valid_len+1];
    char code_sc[serial_len + valid_len + 11];
    memset(serial_code,0,sizeof(serial_code));
    memset(vc,0,sizeof(vc));
    memset(code_sc,0,sizeof(code_sc));

    itoc62(value,serial_code,serial_len);
    get62vc(serial_code,vc,valid_len);
    sprintf(code_sc,"%s%s%s",mch_code,serial_code,vc);
    if (*version=='0'|| *version == '1' || *version == '2') {
        disorder(code_sc, strlen(code_sc));
    }
    else if(*version == '4'){
        lc62toc36(code_sc);
        disorder_by_random(code_sc, randomchar36, RANDOM_CHAR36_LEN, randomchar36[0]);
    }
    else {
        disorder_by_random(code_sc, randomchar62, RANDOM_CHAR62_LEN, char62set);
    }
    sprintf(code,"%s%s",version,code_sc);
    //strncpy(pub_code,serial_code,serial_len);
    itoc32(value,pub_code,PUB_CODE_DE_LEN);
    disorder(pub_code,strlen(pub_code));
    codec_Encode(codec,pub_code);
//    codec_Delete(codec);
}
PHP_FUNCTION(hls_decode){
    zval *zopt, **data;
    char *name;
    char *code,*version;
    uint64_t mch_code_len,serial_len,valid_len,value,if_pub_code;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "a", &zopt) == FAILURE) {
        RETURN_FALSE;
    }
    name="version";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    version=Z_STRVAL_PP(data);
    name="code";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    code=Z_STRVAL_PP(data);
    name="mch_code_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    mch_code_len=Z_LVAL_PP(data);
    name="serial_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    serial_len=Z_LVAL_PP(data);
    name="valid_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    valid_len=Z_LVAL_PP(data);
    name="if_pub_code";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        if_pub_code=0;
    }
    else{
        if_pub_code=Z_LVAL_PP(data);
    }
    int code_len=strlen(code);
    char code_de[code_len+1];
    char mch_code[mch_code_len+1];
    char serial_code[serial_len+1];
    char valid_code[valid_len+1];
    memset(code_de,0,sizeof(code_de));
    memset(mch_code,0,sizeof(mch_code));
    memset(serial_code,0,sizeof(serial_code));
    memset(valid_code,0,sizeof(valid_code));
    strncpy(code_de,code,code_len);
    char *p=code_de;
    p+=strlen(version);
    //decodestr(p,code_len-strlen(version));

    if (*version == '0' || *version == '1' || *version == '2') {
        order(p, code_len - strlen(version));
    }
    else if (*version == '4') {
        order_by_random(p, randomchar36, RANDOM_CHAR36_LEN, randomchar36[0]);
        lc36toc62(p);
    }
    else {
        order_by_random(p, randomchar62, RANDOM_CHAR62_LEN, char62set);
    }
    strncpy(mch_code,p,mch_code_len);
    p+=mch_code_len;
    if (*p != '?') {
        strncpy(serial_code,p,serial_len);
        p+=serial_len;
        strncpy(valid_code,p,valid_len);
        value=c62toi(serial_code);
    }
    array_init(return_value);
    add_assoc_string(return_value, "version", version, 1);
    add_assoc_string(return_value, "mch_code", mch_code, 1);
    add_assoc_string(return_value, "serial_code", serial_code, 1);
    add_assoc_string(return_value, "valid_code", valid_code, 1);
    add_assoc_string(return_value, "code_de", code_de, 1);
    add_assoc_long(return_value, "value",value);
    if(if_pub_code){
        //void *codec=codec_Create(NULL);
        char pub_code[PUB_CODE_EN_LEN+1];
        memset(pub_code,0,sizeof(pub_code));
        itoc32(value,pub_code,PUB_CODE_DE_LEN);
        disorder(pub_code,PUB_CODE_EN_LEN);
        codec_Encode(codec,pub_code);
        add_assoc_string(return_value, "pub_code",pub_code,1);
    }
}

PHP_FUNCTION(hls_batch){
    zval *zopt, **data,*ret;
    char *name,*prefix,*version,*mch_code;
    uint64_t serial_len,valid_len,start,num,if_pub_code;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "a", &zopt) == FAILURE) {
        RETURN_FALSE;
    }
    name="prefix";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    prefix=Z_STRVAL_PP(data);
    name="version";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    version=Z_STRVAL_PP(data);
    name="mch_code";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    mch_code=Z_STRVAL_PP(data);
    name="serial_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    serial_len=Z_LVAL_PP(data);
    name="valid_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    valid_len=Z_LVAL_PP(data);
    name="start";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    start=Z_LVAL_PP(data);
    name="num";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    num=Z_LVAL_PP(data);
    name="if_pub_code";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        if_pub_code=0;
    }else{
        if_pub_code=Z_LVAL_PP(data);
    }
    char code[strlen(version)+strlen(mch_code)+serial_len+valid_len+11];//1
    char pub_code[PUB_CODE_EN_LEN+1];

    int64_t i;
    for(i=start;i<start+num;i++){
        memset(code,0,sizeof(code));
        memset(pub_code,0,sizeof(pub_code));
        encode(version,mch_code,serial_len,valid_len,i,code,pub_code);
        if(if_pub_code){
            php_printf("%s%s,%s\r\n",prefix,code,pub_code);
        }
        else{
            php_printf("%s%s\r\n",prefix,code);
        }
    }
    RETURN_TRUE;
}

PHP_FUNCTION(hls_encode){
    zval *zopt, **data,*ret;
    char *name,*prefix,*version,*mch_code;
    uint64_t serial_len,valid_len,value,if_pub_code;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "a", &zopt) == FAILURE) {
        RETURN_FALSE;
    }
    name="prefix";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    prefix=Z_STRVAL_PP(data);
    name="version";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    version=Z_STRVAL_PP(data);
    name="mch_code";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    mch_code=Z_STRVAL_PP(data);
    name="serial_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    serial_len=Z_LVAL_PP(data);
    name="valid_len";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    valid_len=Z_LVAL_PP(data);
    name="value";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        php_printf("缺少%s参数",name);
        RETURN_FALSE;
    }
    value=Z_LVAL_PP(data);
    name="if_pub_code";
    if (zend_hash_find(Z_ARRVAL_P(zopt),name , strlen(name)+1, (void**)&data) == FAILURE) {
        if_pub_code=0;
    }else{
        if_pub_code=Z_LVAL_PP(data);
    }
    char code[strlen(version)+serial_len+valid_len+11];//1
    char pub_code[PUB_CODE_DE_LEN+1];
    char full_code[strlen(prefix)+sizeof(code)+11];//1
    memset(code,0,sizeof(code));
    memset(pub_code,0,sizeof(pub_code));
    memset(full_code,0,sizeof(full_code));
    encode(version,mch_code,serial_len,valid_len,value,code,pub_code);
    /*php_printf("value:%s serial_code:%s\n",value,serial_code);*/
    sprintf(full_code,"%s%s",prefix,code);
    array_init(return_value);
    add_assoc_string(return_value, "code", code, 1);
    add_assoc_string(return_value, "full_code", full_code, 1);
    if(if_pub_code){
        add_assoc_string(return_value, "pub_code", pub_code, 1);
    }
}

PHP_FUNCTION(hls_decode_pub){
    char *pub_code,pub_code_de[10];
    uint64_t len,value;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &pub_code,&len) == FAILURE) {
        RETURN_FALSE;
    }
    memset(pub_code_de,0,sizeof(pub_code_de));
    memcpy(pub_code_de,pub_code,sizeof(pub_code_de));
    int error=codec_Decode(codec,pub_code_de);
    pub_code_de[7]=0;
    order(pub_code_de,strlen(pub_code_de));
    value=c32toi(pub_code_de);
    array_init(return_value);
    add_assoc_string(return_value, "code", pub_code_de, 1);
    add_assoc_long(return_value,"value",value);
}

PHP_FUNCTION(hls_encode_pub){
    char code[10];
    uint64_t value;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &value) == FAILURE) {
        RETURN_FALSE;
    }
    memset(code,0,sizeof(code));
    itoc32(value,code,PUB_CODE_DE_LEN);
    disorder(code,strlen(code));
    codec_Encode(codec,code);
    RETURN_STRING(code,1);
}

PHP_MINIT_FUNCTION(hls)
{
    int i=0;
    codec=(void*)codec_Create(NULL);
    memset(char62map,0,sizeof(char62map));
    memset(char32map,0,sizeof(char32map));
    for(i=0;i<sizeof(char62set);i++){
        char62map[char62set[i]]=i;
    }
    for(i=0;i<sizeof(char32set);i++){
        char32map[char32set[i]]=i;
    }
    return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(hls)
{
    codec_Delete(codec);
    return SUCCESS;
}

static zend_function_entry hls_functions[] = {
    PHP_FE(hls_encode, NULL)
    PHP_FE(hls_decode, NULL)
    PHP_FE(hls_decode_pub, NULL)
    PHP_FE(hls_encode_pub, NULL)
    PHP_FE(hls_batch, NULL)
    {NULL, NULL, NULL}
};

zend_module_entry hls_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    "hls",
    hls_functions,
    PHP_MINIT(hls),
    PHP_MSHUTDOWN(hls),
    NULL,
    NULL,
    NULL,
#if ZEND_MODULE_API_NO >= 20010901
    "0.1",
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_HLS
    ZEND_GET_MODULE(hls)
#endif


