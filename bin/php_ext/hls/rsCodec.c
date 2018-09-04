
#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#include "rsCodecImpl.h"

void free_rs_char( void *p )
{
	RS_S *rs;

	if( NULL != p )
	{
		rs = (RS_S*)p;
		free(rs->buff);
		free(rs->alpha_to);
		free(rs->index_of);
		free(rs->genpoly);
		free(rs);
		p = NULL;
	}
	return;
}

void *init_rs_char( int symsize,int gfpoly,int fcr,int prim, int nroots,int pad )
{
	RS_S *rs;
	int i, j, sr,root,iprim;
	
	if(symsize < 0 || symsize > 8*sizeof(DTYPE))
		return NULL;
	
	if(fcr < 0 || fcr >= (1<<symsize))
		return NULL;
	if(prim <= 0 || prim >= (1<<symsize))
		return NULL;
	if(nroots < 0 || nroots >= (1<<symsize))
		return NULL;
	if(pad < 0 || pad >= ((1<<symsize) -1 - nroots))
		return NULL;
	
	rs = (RS_S *)calloc(1,sizeof(RS_S));
	rs->mm = symsize;
	rs->nn = (1<<symsize)-1;
	rs->pad = pad;
	
	rs->alpha_to = (DTYPE *)malloc(sizeof(DTYPE)*(rs->nn+1));
	if(rs->alpha_to == NULL)
	{
		free(rs);
		return NULL;
	}
	rs->index_of = (DTYPE *)malloc(sizeof(DTYPE)*(rs->nn+1));
	if(rs->index_of == NULL)
	{
		free(rs->alpha_to);
		free(rs);
		return NULL;
	}
	
	rs->index_of[0] = (unsigned char)A0;
	rs->alpha_to[A0] = 0;
	sr = 1;
	for(i=0;i<rs->nn;i++)
	{
		rs->index_of[sr] = (unsigned char)i;
		rs->alpha_to[i] = (unsigned char)sr;
		sr <<= 1;
		if(sr & (1<<symsize))
			sr ^= gfpoly;
		sr &= rs->nn;
	}
	if(sr != 1)
	{
		free(rs->alpha_to);
		free(rs->index_of);
		free(rs);
		return NULL;
	}
	
	rs->genpoly = (DTYPE *)malloc(sizeof(DTYPE)*(nroots+1));
	if(rs->genpoly == NULL)
	{
		free(rs->alpha_to);
		free(rs->index_of);
		free(rs);
		return NULL;
	}
	rs->fcr = fcr;
	rs->prim = prim;
	rs->nroots = nroots;
	rs->buff = (DTYPE*)malloc((NROOTS*8+8)*sizeof(DTYPE));
	if( rs->buff==NULL )
	{
		free(rs->genpoly);
		free(rs->alpha_to);
		free(rs->index_of);
		free(rs);
		return NULL;
	}
	
	for(iprim=1;(iprim % prim) != 0;iprim += rs->nn)
		;
	rs->iprim = iprim / prim;
	
	rs->genpoly[0] = 1;
	for (i = 0,root=fcr*prim; i < nroots; i++,root += prim)
	{
		rs->genpoly[i+1] = 1;
		for (j = i; j > 0; j--)
		{
			if (rs->genpoly[j] != 0)
				rs->genpoly[j] = rs->genpoly[j-1] ^ rs->alpha_to[modnn(rs,rs->index_of[rs->genpoly[j]] + root)];
			else
				rs->genpoly[j] = rs->genpoly[j-1];
		}
		rs->genpoly[0] = rs->alpha_to[modnn(rs,rs->index_of[rs->genpoly[0]] + root)];
	}
	for (i = 0; i <= nroots; i++)
		rs->genpoly[i] = rs->index_of[rs->genpoly[i]];
	
	return rs;
}

void encode_rs_char( void *p, DTYPE *data, DTYPE *bb )
{
	RS_S *rs = (RS_S*)p;
	int i, j;
	DTYPE feedback;
	
	memset( bb, 0, NROOTS*sizeof(DTYPE) );
	
	for( i=0; i<NN-NROOTS-PAD; i++ )
	{
		feedback = INDEX_OF[data[i] ^ bb[0]];
		if( feedback != A0 )
		{
			for(j=1;j<NROOTS;j++)
				bb[j] ^= ALPHA_TO[MODNN(feedback + GENPOLY[NROOTS-j])];
		}
		memmove(&bb[0],&bb[1],sizeof(DTYPE)*(NROOTS-1));
		if( feedback != A0 )
			bb[NROOTS-1] = ALPHA_TO[MODNN(feedback + GENPOLY[0])];
		else
			bb[NROOTS-1] = 0;
	}
}


int decode_rs_char( void *p, DTYPE *data, int *eras_pos, int no_eras )
{
	RS_S *rs = (RS_S*)p;

	int deg_lambda, el, deg_omega;
	int i, j, r,k;
	DTYPE u,q,tmp,num1,num2,den,discr_r;
	int syn_error, count;
	int iCorrect = 0;
	DTYPE dataBak[256];

	DTYPE *lambda, *s;
	DTYPE *b, *t, *omega;
	DTYPE *root, *reg, *loc;
 
	memset( rs->buff, 0, (NROOTS*8+8)*sizeof(DTYPE));

	lambda = rs->buff;
	s = lambda + (NROOTS+1);
	b = s + (NROOTS+1);
	t = b + (NROOTS+1);
	omega = t + (NROOTS+1);
	root = omega + (NROOTS+1);
	reg = root + (NROOTS+1);
	loc = reg + (NROOTS+1);

	memcpy( dataBak, data, sizeof(DTYPE)*(NN-PAD) );

	for(i=0;i<NROOTS;i++)
		s[i] = data[0];
	
	for(j=1;j<NN-PAD;j++)
	{
		for(i=0;i<NROOTS;i++)
		{
			if(s[i] == 0)
			{
				s[i] = data[j];
			}
			else
			{
				s[i] = data[j] ^ ALPHA_TO[MODNN(INDEX_OF[s[i]] + (FCR+i)*PRIM)];
			}
		}
	}
	
	syn_error = 0;
	for(i=0;i<NROOTS;i++)
	{
		syn_error |= s[i];
		s[i] = INDEX_OF[s[i]];
	}
	
	if (!syn_error)
	{
		count = 0;
		goto finish;
	}
	lambda[0] = 1;
	memset( lambda+1, 0, NROOTS*sizeof(DTYPE) );
	
	if( no_eras > 0 )
	{
		lambda[1] = ALPHA_TO[MODNN(PRIM*(NN-1-eras_pos[0]))];
		for (i = 1; i < no_eras; i++)
		{
			u = (unsigned char)MODNN(PRIM*(NN-1-eras_pos[i]));
			for (j = i+1; j > 0; j--)
			{
				tmp = INDEX_OF[lambda[j - 1]];
				if(tmp != A0)
					lambda[j] ^= ALPHA_TO[MODNN(u + tmp)];
			}
		}
	}
	for(i=0;i<NROOTS+1;i++)
		b[i] = INDEX_OF[lambda[i]];

	r = no_eras;
	el = no_eras;
	while (++r <= NROOTS)
	{
		discr_r = 0;
		for (i = 0; i < r; i++)
		{
			if ((lambda[i] != 0) && (s[r-i-1] != A0))
			{
				discr_r ^= ALPHA_TO[MODNN(INDEX_OF[lambda[i]] + s[r-i-1])];
			}
		}
		discr_r = INDEX_OF[discr_r];
		if (discr_r == A0)
		{
			memmove(&b[1],b,NROOTS*sizeof(DTYPE));
			b[0] = (unsigned char)A0;
		}
		else
		{
			t[0] = lambda[0];
			for (i = 0 ; i < NROOTS; i++)
			{
				if(b[i] != A0)
					t[i+1] = lambda[i+1] ^ ALPHA_TO[MODNN(discr_r + b[i])];
				else
					t[i+1] = lambda[i+1];
			}
			if (2 * el <= r + no_eras - 1)
			{
				el = r + no_eras - el;
				for (i = 0; i <= NROOTS; i++)
					b[i] = (lambda[i] == 0) ? A0 : (unsigned char)MODNN(INDEX_OF[lambda[i]] - discr_r + NN);
			}
			else
			{
				memmove(&b[1],b,NROOTS*sizeof(DTYPE));
				b[0] = (unsigned char)A0;
			}
			memcpy(lambda,t,(NROOTS+1)*sizeof(DTYPE));
		}
	}
	
	deg_lambda = 0;
	for(i=0;i<NROOTS+1;i++)
	{
		lambda[i] = INDEX_OF[lambda[i]];
		if(lambda[i] != A0)
			deg_lambda = i;
	}

	memcpy(&reg[1],&lambda[1],NROOTS*sizeof(DTYPE));
	count = 0;
	for (i = 1,k=IPRIM-1; i <= NN; i++,k = MODNN(k+IPRIM))
	{
		q = 1;
		for (j = deg_lambda; j > 0; j--)
		{
			if (reg[j] != A0)
			{
				reg[j] = (unsigned char)MODNN(reg[j] + j);
				q ^= ALPHA_TO[reg[j]];
			}
		}
		if (q != 0)
			continue;
		root[count] = (unsigned char)i;
		loc[count] = (unsigned char)k;
		if(++count == deg_lambda)
			break;
	}
	if (deg_lambda != count)
	{
		count = -1;
		goto finish;
	}
	deg_omega = deg_lambda-1;
	for (i = 0; i <= deg_omega;i++)
	{
		tmp = 0;
		for(j=i;j >= 0; j--)
		{
			if ((s[i - j] != A0) && (lambda[j] != A0))
				tmp ^= ALPHA_TO[MODNN(s[i - j] + lambda[j])];
		}
		omega[i] = INDEX_OF[tmp];
	}
	
	for (j = count-1; j >=0; j--)
	{
		num1 = 0;
		for (i = deg_omega; i >= 0; i--)
		{
			if (omega[i] != A0)
				num1  ^= ALPHA_TO[MODNN(omega[i] + i * root[j])];
		}
		num2 = ALPHA_TO[MODNN(root[j] * (FCR - 1) + NN)];
		den = 0;
		
		for (i = MIN(deg_lambda,NROOTS-1) & ~1; i >= 0; i -=2)
		{
			if(lambda[i+1] != A0)
				den ^= ALPHA_TO[MODNN(lambda[i+1] + i * root[j])];
		}
		if (num1 != 0 && loc[j] >= PAD)
		{
			data[loc[j]-PAD] ^= ALPHA_TO[MODNN(INDEX_OF[num1] + INDEX_OF[num2] + NN - INDEX_OF[den])];
		}
	}
finish:
	if(eras_pos != NULL)
	{
		for( i=0; i<count; i++ )
			eras_pos[i] = loc[i]-PAD;
	}

	for( j=0; j<NN-PAD; j++ )
		if( data[j]^dataBak[j] )
			iCorrect++;
	if( count>0 && iCorrect==0 )
		return -1;
	else
		return count;
}

