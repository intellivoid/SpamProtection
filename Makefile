clean:
	rm -rf build

build:
	mkdir build
	ppm --compile="src/SpamProtection" --directory="build"

install:
	ppm --fix-conflict --no-prompt --install="build/net.intellivoid.spam_protection.ppm"

update_package:
	ppm --generate-package="src/SpamProtection"