
#ifndef _CODEC_H_
#define _CODEC_H_
#ifdef linux
#define __stdcall
#endif

// #ifdef _cplusplus
// extern "C" {
// #endif

void* __stdcall codec_Create( const char* pCode );



int __stdcall codec_Encode( void* pHandle, char* szToEncode );

int __stdcall codec_Decode( void* pHandle, char* szToDecode );

void __stdcall codec_Delete( void* pHandle );

// #ifdef _cplusplus
// }
// #endif

#endif

