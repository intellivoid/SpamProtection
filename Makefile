clean:
	rm -rf build

build:
	mkdir build
<<<<<<< HEAD
	ppm --no-intro --compile="src/SpamProtection" --directory="build"

install:
	ppm --no-intro --no-prompt --install="build/net.intellivoid.spam_protection.ppm"
=======
	ppm --compile="src/SpamProtection" --directory="build"

install:
	ppm --fix-conflict --no-prompt --install="build/net.intellivoid.spam_protection.ppm"

update_package:
	ppm --generate-package="src/SpamProtection"
>>>>>>> 1.2.0.0
