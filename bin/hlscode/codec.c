#ifdef linux
#define __stdcall
#endif
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "rsCodec.h"

static char g_szMyCode[40] = "ABCDEFGH0123456789JKLMNPRTUVWXYZ";
static unsigned char g_szMyDecode[128] =
{
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    8,9,10,11,12,13,14,15,16,17,0,0,0,0,0,0,
    0,0,1,2,3,4,5,6,7,0,18,19,20,21,22,0,
    23,0,24,0,25,26,27,28,29,30,31,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
};

typedef struct
{
    void* rs;
    char szMyCode[32];
    unsigned char szMyDecode[128];
}codec_S;

void* __stdcall codec_Create( const char* pCode )
{
    codec_S* pstcodec = (codec_S*)malloc( sizeof(codec_S) );
    if( NULL == pstcodec )
        return NULL;

    pstcodec->rs = init_rs_char( 5, 0x25, 1, 1, 2, 22 );
    if( NULL == pstcodec->rs )
    {
        free( pstcodec );
        return NULL;
    }

    memcpy( pstcodec->szMyCode, g_szMyCode, 32 );
    memcpy( pstcodec->szMyDecode, g_szMyDecode, 128 );

    return pstcodec;
}

/*2^35 ---- 320*/
/*int __stdcall codec_Encode( void* pHandle, unsigned int iHigh, unsigned int iLow, char* szOut )*/
/*{*/
/*int i;*/
/*unsigned char szBuff[32];*/
/*codec_S* pstcodec = (codec_S*)pHandle;*/

/*memset( szBuff, 0, 32 );*/

/*for( i=0; i<6; i++ )*/
/*{*/
/*szBuff[6-i] = iLow & 31;*/
/*iLow >>= 5;*/
/*}*/
/*szBuff[0] = ( (iHigh<<2) + iLow ) & 31;*/

/*encode_rs_char( pstcodec->rs, szBuff, szBuff+7 );*/

/*for( i=0; i<9; i++ )*/
/*szOut[i] = pstcodec->szMyCode[szBuff[i]&31];*/
/*szOut[i] = 0;*/

/*return 0;*/
/*}*/


/*int __stdcall codec_Decode( void* pHandle, unsigned int* piHigh, unsigned int* piLow, char* szIn )*/
/*{*/
/*int i, iError, tmp;*/
/*unsigned char szBuff[32];*/
/*unsigned char szBuff_b[32];*/
/*int eras_pos[10];*/
/*codec_S* pstcodec = (codec_S*)pHandle;*/

/*memset( szBuff, 0, 32 );*/

/*for( i=0; i<9; i++ )*/
/*szBuff[i] = pstcodec->szMyDecode[szIn[i]&127];*/

/*iError = decode_rs_char( pstcodec->rs, szBuff, eras_pos, 0 );*/

/*if( iError >= 0 )*/
/*{*/
/*memcpy( szBuff_b, szBuff, 7 );*/
/*szBuff_b[7] = szBuff_b[8] = 0;*/
/*encode_rs_char( pstcodec->rs, szBuff_b, szBuff_b+7 );*/
/*if( szBuff_b[7] != szBuff[7] || szBuff_b[8]!=szBuff[8] )*/
/*return -100;*/


/*tmp = 0;*/
/*for( i=0; i<7; i++ )*/
/*{*/
/*tmp <<= 5;*/
/*tmp |= szBuff[i];*/
/*}*/
/**piLow = tmp;*/
/**piHigh = (szBuff[0]>>2)&7;*/

/*return 0;*/
/*}*/
/*else*/
/*{*/
/**piHigh = 0;*/
/**piLow = 0;*/
/*return -100;*/
/*}*/
/*}*/

// 2^35 ---- 320
int __stdcall codec_Encode( void* pHandle, char* szToEncode )
{
    int i;
    unsigned char szBuff[32];
    codec_S* pstcodec = (codec_S*)pHandle;

    memset( szBuff, 0, 32 );

    for( i=0; i<7; i++ ){
        szBuff[i] = pstcodec->szMyDecode[szToEncode[i]&127];
    }
    encode_rs_char( pstcodec->rs, szBuff, szBuff+7 );
    for( i=0; i<9; i++ ){
        szToEncode[i] = pstcodec->szMyCode[szBuff[i]&31];
    }
    szToEncode[i] = 0;

    return 0;
}


int __stdcall codec_Decode( void* pHandle, char* szToDecode )
{
    int i, iError;
    unsigned char szBuff[32];
    unsigned char szBuff_b[32];
    int eras_pos[10];
    codec_S* pstcodec = (codec_S*)pHandle;

    memset( szBuff, 0, 32 );
    for( i=0; i<9; i++ ){
        szBuff[i] = pstcodec->szMyDecode[szToDecode[i]&127];
    }
    iError = decode_rs_char( pstcodec->rs, szBuff, eras_pos, 0 );
    if( iError >= 0 )
    {
        memcpy( szBuff_b, szBuff, 7 );
        szBuff_b[7] = szBuff_b[8] = 0;
        encode_rs_char( pstcodec->rs, szBuff_b, szBuff_b+7 );
        if( szBuff_b[7] != szBuff[7] || szBuff_b[8]!=szBuff[8] )
            return -100;
        for( i=0; i<7; i++ ){
            szToDecode[i] = pstcodec->szMyCode[szBuff_b[i]];
        }

        return iError;
    }
    else
    {
        return -1;
    }
}

void __stdcall codec_Delete( void* pHandle )
{
    codec_S* pstcodec = (codec_S*)pHandle;
    if( pstcodec )
    {
        if( pstcodec->rs )
            free_rs_char( pstcodec->rs );
        free( pstcodec );
    }
    return;
}

