PKGNAME = ezcbt
BASEDIR = .
DEVDIR = $(BASEDIR)/presets/development
PRODDIR = $(BASEDIR)/presets/production

COPY = cp -vr

default: build

.PHONY : build

init:
	@ echo 'Initializing ...'
	@ ./init
	@ echo 'Done'

.PHONY : init

build: init
	@ echo 'Building frontend application ...'
	@ cd frontend; npm run build
	@ echo 'Done'

.PHONY : build

development:
	@ echo 'Setting up development environment ...'
	@ $(COPY) $(DEVDIR)/* $(BASEDIR)
	@ echo 'Done'

.PHONY : development

production:
	@ echo 'Setting up production environment ...'
	@ $(COPY) $(PRODDIR)/* $(BASEDIR)
	@ echo 'Done'

.PHONY : production

archive: production build
	@ echo 'Preparing archive for distribution ...'
	@ tar -czf $(PKGNAME).tar.gz application public system index.php ezcbt.sql
	@ echo 'Done.'

.PHONY : archive

