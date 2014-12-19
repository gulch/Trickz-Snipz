#!/bin/bash

## storing current dir
pushd . > /dev/null

### Installing needed packages
apt-get install -y advancecomp libimage-exiftool-perl imagemagick \
    optipng libjpeg-progs gifsicle pngnq \
    tar unzip libpng-dev git

### Installing additional software
mkdir /tmp/imgo-installation/bin -p
cd /tmp/imgo-installation

# messages - log for warnings
messages=/tmp/imgo-installation/messages

### I reccomend to launch commands above manually! One by one. It could be very-very sad bad because you can catch some errors. Use it at your own risk!

# pngout
wget http://static.jonof.id.au/dl/kenutils/pngout-20130221-linux-static.tar.gz -O pngout.tar.gz
if [ -e pngout.tar.gz ];
then
    tar -xvf pngout.tar.gz
    cp pngout-20130221-linux-static/`uname -m`/pngout-static ./bin/pngout
else
    echo "   * pngout not installed" >> ${messages}
fi

# defluff. WARNING! There are i686 and x86_64 binaries only
wget https://github.com/imgo/imgo-tools/raw/master/src/defluff/defluff-0.3.2-linux-`uname -m`.zip -O defluff.zip
if [ -e defluff.zip ];
then
    unzip defluff.zip
    chmod a+x defluff
    cp defluff /tmp/imgo-installation/bin
else
    echo "   * defluff not installed" >> ${messages}
fi

# cryopng
wget http://frdx.free.fr/cryopng/cryopng-linux-x86.tgz -O cryo.tgz
if [ -e cryo.tgz ];
then
    tar -zxf cryo.tgz
    cp cryo-files/cryopng /tmp/imgo-installation/bin
else
    echo "   * cryopng not installed" >> ${messages}
fi

# pngrewrite. building from sources. binaries only for win
# Do you really need pngrewrite? http://entropymine.com/jason/pngrewrite/
mkdir pngrewrite && cd pngrewrite/
wget http://entropymine.com/jason/pngrewrite/pngrewrite-1.4.0.zip -O pngrewrite.zip
if [ -e pngrewrite.zip ];
then
    unzip pngrewrite.zip
    make
    cp ./pngrewrite /tmp/imgo-installation/bin
else
    echo "   * pngrewrite not installed" >> ${messages}
fi
cd ..

# imgo script. Yeah! Finally
git clone git://github.com/imgo/imgo.git
cp imgo/imgo /tmp/imgo-installation/bin/

# copy binaries to your local ~/bin or global /usr/local/bin
# mkdir -p ~/bin && cp /tmp/imgo-installation/bin/* ~/bin # or
cp /tmp/imgo-installation/bin/* /usr/local/bin/

# show warnings summary after install complete
if [ -s "${messages}" ]; then cat ${messages}; fi

# dir restore and clean up
popd > /dev/null
rm -rf /tmp/imgo-installation