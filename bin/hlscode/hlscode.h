#include "string.h"
typedef unsigned long long int uint64_t;

typedef struct {
	char version[8];
	char prefix[256];
	char mch_code[8];
	uint64_t serial_len;
	uint64_t valid_len;
} code_def;

typedef struct {
	char code[32];
	char pubcode[32];
	char fullcode[256];
} code_en;


typedef struct {
	char mch_code[8];
	char serial_code[32];
	char valid_code[8];
	uint64_t v;
} code_de;

//通过整数获得混淆的乐码
code_en* hls_encode(uint64_t v,code_def* def,code_en* en);
//解密混淆过的乐码
code_de* hls_decode(char* code,code_def* def,code_de* de);
//将外码解密为整数
uint64_t hls_pub_decode(char* pubcode);
//通过外码得到解密的乐码
code_de* hls_pub_code_de(char* pubcode,code_def* def,code_de* de);
//通过外码获得加密的乐码
code_en* hls_pub_code_en(char* pubcode,code_def* def,code_en* de);
