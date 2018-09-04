
#ifndef _RS_CODEC_H_
#define _RS_CODEC_H_

void *init_rs_char( int symsize, int gfpoly, int fcr, int prim, int nroots, int pad );
void encode_rs_char( void *rs, unsigned char *data, unsigned char *parity );
int decode_rs_char( void *rs, unsigned char *data, int *eras_pos, int no_eras );
void free_rs_char( void *rs );

#endif

