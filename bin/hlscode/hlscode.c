#include "codec.h"
#include "math.h"
#include "stdio.h"
#include "hlscode.h"
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

int init_res(){
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
}


void encode(char *version,char* mch_code,uint64_t serial_len,uint64_t valid_len,uint64_t value,char *code,char *pub_code){
    char format[36];
    char serial_code[serial_len+1];
    char vc[valid_len+1];
    char code_sc[serial_len+valid_len+1];
    memset(serial_code,0,sizeof(serial_code));
    memset(vc,0,sizeof(vc));
    memset(code_sc,0,sizeof(code_sc));

    itoc62(value,serial_code,serial_len);
    get62vc(serial_code,vc,valid_len);
    sprintf(code_sc,"%s%s%s",mch_code,serial_code,vc);
    disorder(code_sc,strlen(code_sc));
    sprintf(code,"%s%s",version,code_sc);
    //strncpy(pub_code,serial_code,serial_len);
    itoc32(value,pub_code,PUB_CODE_DE_LEN);
    disorder(pub_code,PUB_CODE_DE_LEN);
    codec_Encode(codec,pub_code);
	//printf("encode::::::::: code:%s  pub_code:%s\r\n",code,pub_code);
//    codec_Delete(codec);
}

code_en* hls_encode(uint64_t v,code_def* def,code_en* en){
	init_res();
	//printf("hls_encode:version %s mch_code %s code %s pubcode %s\r\n",def->version,def->mch_code,en->code,en->pubcode);
    encode(def->version,def->mch_code,def->serial_len,def->valid_len,v,en->code,en->pubcode);
    sprintf(en->fullcode,"%s%s",def->prefix,en->code);
    return en;
}

code_de* hls_decode(char* code,code_def* def,code_de* de){
	init_res();
	char tmp[strlen(code)+1];
	memset(tmp,0,sizeof(tmp));
	strncpy(tmp,code,sizeof(tmp)-1);
	char *p=tmp;
	p+=strlen(def->prefix);
    p+=strlen(def->version);
    //decodestr(p,code_len-strlen(version));
    order(p,strlen(p));
    strncpy(de->mch_code,p,strlen(def->mch_code));
    p+=strlen(def->mch_code);
    strncpy(de->serial_code,p,def->serial_len);
    p+=def->serial_len;
    strncpy(de->valid_code,p,def->valid_len);
    de->v=c62toi(de->serial_code);
	return de;
}

//将外码解密为整数
uint64_t hls_pub_decode(char* pubcode){
	char pubcode_de[10];
    uint64_t len,value;

	init_res();
    memset(pubcode_de,0,sizeof(pubcode_de));
    memcpy(pubcode_de,pubcode,sizeof(pubcode_de));
    int error=codec_Decode(codec,pubcode_de);
    pubcode_de[PUB_CODE_DE_LEN]=0;
    order(pubcode_de,strlen(pubcode_de));
    value=c32toi(pubcode_de);
    return value;
}
//通过外码得到解密的乐码
code_de* hls_pub_code_de(char* pubcode,code_def* def,code_de* de){
	code_en en;
	uint64_t v=hls_pub_decode(pubcode);
	hls_encode(v,def,&en);
	return hls_decode(en.fullcode,def,de);
}
//通过外码获得加密的乐码
code_en* hls_pub_code_en(char* pubcode,code_def* def,code_en* en){
	uint64_t v=hls_pub_decode(pubcode);
	printf("hls_pub_code_en  v:%d\r\n",v);
	return hls_encode(v,def,en);
}

int main(){
	code_def def;
	code_de de;
	code_en en;
	memset(&def,0,sizeof(code_def));
	memset(&de,0,sizeof(code_de));
	memset(&en,0,sizeof(code_en));
	strcpy(def.mch_code,"01");
	strcpy(def.version,"0");
	strcpy(def.prefix,"http://lsa0.cn/c/");
	def.serial_len=6;
	def.valid_len=2;
	hls_encode(10,&def,&en);
	printf("code:%s fullcode:%s\r\n",en.code,en.fullcode);
	hls_decode(en.fullcode,&def,&de);
	printf("mch_code %s v:%d\r\n",de.mch_code,de.v);
	printf("pubcode:%d\r\n",hls_pub_decode(en.pubcode));
	char pubcode_tmp[strlen(en.pubcode)+1];
	memset(pubcode_tmp,0,sizeof(pubcode_tmp));
	strcpy(pubcode_tmp,en.pubcode);
	memset(&de,0,sizeof(code_de));
	memset(&en,0,sizeof(code_en));
	hls_pub_code_de(pubcode_tmp,&def,&de);
	hls_pub_code_en(pubcode_tmp,&def,&en);
	printf("code:%s fullcode:%s\r\n",en.code,en.fullcode);
	printf("mch_code %s v:%d\r\n",de.mch_code,de.v);
	getchar();
}
/* int hls_batch(){
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
    char code[strlen(prefix)+strlen(version)+serial_len+valid_len+1];
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





int hls_decode_pub(){
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

int hls_encode_pub(){
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
}*/


