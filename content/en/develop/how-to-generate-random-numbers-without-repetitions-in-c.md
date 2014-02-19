<!--
Title: Generate random array without repetitions in&nbsp;C&nbsp;?
Description: How to generate random numbers without repetitions in C
Date: 2013/12/14
Tags: C, developing, alghoritms
-->

<!-- http://www.wasm.ru/forum/viewtopic.php?pid=189777#p189777 -->

Let's asume, You need to generate random numbers without repetitions.
If you know the range (between 0 and N_MAX) and the count,
you have many ways to implement this. The best one, in my opinion, I will show below<!--cut-here-->.

The idea is deadly simple - you generate the array of nessesary numbers&nbsp;(structures/odjects/etc)
and.. shuffle it :)
For example, with Donald&nbsp;Knuth alghoritm ("The Art of Computer Programming", vol.2, Alg.3.4.2.P).

Here's rudimental example in C:

	/**
	 * Knut's shuffle example.
	 * gcc -Wall -Wextra -std=gnu99 random_array.c -o random
	 */

	#include <time.h>
	#include <stdlib.h>
	#include <stdio.h>

	int rand_lim(int limit);

	int main(int argc, char* argv[])
	{
		int array_size = 10;
		if (argc > 1)
			array_size = atoi(argv[1]);

		if (array_size < 1) {
			printf("Usage: %s <size of array>\n", argv[0]);
			exit(1);
		}
		else
			printf("use array of %d elements\n", array_size);


		int i, j, m;
		int *a;	/* just a dynamic array */

		a = (int *) malloc(array_size * sizeof(*a));
		if (NULL == a) {
			printf("Memory fails");
			exit(1);
		}

		/* init array with ints between 0..array_size */
		for (i = 0; i < array_size; ++i)
			a[i] = i;


		/* shuffle it */
		srand(time(NULL));

		for(i=0; i < array_size-1; i++)
		{
			m = i + rand_lim(array_size-i-1);
			j = a[i];
			a[i] = a[m];
			a[m] = j;
		}

		for (j=0; j < array_size; ++j) {
			printf("a[%d]= %d\n", j, a[j]);
		}

		free(a);
		return 0;
	}

	/**
	 * return a random number between 0 and limit inclusive.
	 */
	int rand_lim(int limit)
	{
		int divisor = RAND_MAX/(limit+1);
		int retval;

		do { 
			retval = rand() / divisor;
		} while (retval > limit);

		return retval;
	}

**P.s.** It is a *common practice* to get random number in range like

	/* random int between 0 and 19 */
	int r = rand() % 20;

but it is not the correct one. Actually, this is a topic for another article.
In a nutshell, attempts that just use `%` (or, equivalently, `/`) to get the numbers
in a range almost inevitably introduce skew
(i.e., some numbers will be generated more often than others).
You can read more about this [here][SO-generate-random-in-range].

[SO-generate-random-in-range]:
http://stackoverflow.com/questions/2999075/generate-a-random-number-within-range/2999130#2999130 (Generate a random number within range?)
