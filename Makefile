clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/SpamProtection" --directory="build"

install:
	ppm --no-intro --no-prompt --install="build/net.intellivoid.spam_protection.ppm"