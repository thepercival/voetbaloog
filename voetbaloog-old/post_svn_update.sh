#!/bin/bash

CURRENTPATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
#PRIVATEHTML=$CURRENTPATH
#PRIVATEHTML=/var/www/voetbaloog

echo $CURRENTPATH
# do dbupdates and autorizationupdates
XDEBUG_MODE=off php $CURRENTPATH/app_voetbaloog/model/installer/update.php

# shrink jslibs jslibrary and jslibraryvo
PUBLICHTML=$CURRENTPATH/pub_voetbaloog
YUIJAR=$CURRENTPATH/ThirdParties/yuicompressor-2.4.8.jar
MAPDIR=$PUBLICHTML/public/scripts
JSTHPDIR=$PUBLICHTML/jsthirdparties/jsvoetbal/jquery
MINIFIEDJS=$MAPDIR/custom.min.js
COMBINEDJS=$MAPDIR/combined.js

echo "javascript combineren"
if [ -e $MINIFIEDJS ]; then rm $MINIFIEDJS; fi
find $MAPDIR -type f \( -name "custom.js" \) -exec cat {} \; >> $COMBINEDJS.tmp
find $JSTHPDIR -type f \( -name "voetbal.js" \) -exec cat {} \; >> $COMBINEDJS.tmp
mv $COMBINEDJS.tmp $COMBINEDJS
java -jar $YUIJAR --type js $COMBINEDJS -o $MINIFIEDJS
rm $COMBINEDJS

java -jar $YUIJAR --type js $MAPDIR/lazyload.js -o $MAPDIR/lazyload.js.tmp
rm $MAPDIR/lazyload.js
mv $MAPDIR/lazyload.js.tmp $MAPDIR/lazyload.js
echo "javascript neergezet op $MAPDIR/lazyload.js"

# jslibrary
LIBDIR=$PUBLICHTML/jslibrary
MINIFIEDJS=min-1.0.0.js

cd $LIBDIR
if [ -e $MINIFIEDJS ]; then rm $MINIFIEDJS; fi
find . -type f \( -name "*.js" \) -exec cat {} \; > $COMBINEDJS.tmp
mv $COMBINEDJS.tmp $COMBINEDJS
java -jar $YUIJAR --type js $COMBINEDJS -o $MINIFIEDJS
rm $COMBINEDJS

#jslibraryvo
LIBDIR=$MAPDIR/jslibraryvo
MINIFIEDJS=voetbaloog-min-1.0.0.js

cd $LIBDIR
if [ -e $MINIFIEDJS ]; then rm $MINIFIEDJS; fi
find . -type f \( -name "*.js" \) -exec cat {} \; > $COMBINEDJS.tmp
mv $COMBINEDJS.tmp $COMBINEDJS
java -jar $YUIJAR --type js $COMBINEDJS -o $MINIFIEDJS
rm $COMBINEDJS

#css
LIBDIR=$PUBLICHTML/public/styles
MINIFIEDCSS=min-1.0.0.css
COMBINEDCSS=combined.css

cd $LIBDIR
if [ -e $MINIFIEDCSS ]; then rm $MINIFIEDCSS; fi
cat bootstrap_custom.css flaticon.css voetbaloog.css wizard.css > $COMBINEDCSS.tmp

mv $COMBINEDCSS.tmp $COMBINEDCSS
java -jar $YUIJAR --type css $LIBDIR/$COMBINEDCSS -o $MINIFIEDCSS
rm $COMBINEDCSS

rm $CURRENTPATH/app_voetbaloog/cache/*

cd $CURRENTPATH
exit
