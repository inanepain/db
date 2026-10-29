# inanepain/db
# version: $Id$
# date: $Date$

set shell := ["zsh", "-cu"]
set positional-arguments
set default-list := true

project := "inane\\db"

#region base
# list recipes
[default]
_default:
    @echo "{{project}}:"
    @just --list --list-heading ''

# start
[group: 'helpers']
[arg('task', help="New task being run.")]
_start task='':
    @echo "{{project}}: {{GREEN}}start{{NORMAL}}: {{task}}"

# done
[group: 'helpers']
[arg('task', help="Completed task.")]
_done task='':
    @echo "{{project}}: {{GREEN}}done{{NORMAL}} {{task}}"

#endregion base

#region git
# git push all
[group: 'GIT']
git-push-all: (_start "Push All") && (_done "Push All")
    #!/usr/bin/env zsh
    git pushall

#endregion git

#region documentation
# create or update all documentation.
[group: 'doc']
build-doc: (_start "Doc: ALL") && build-phpdoc build-asciidoc (_done "Doc: ALL")

# generate php part (v2) (all, cache, html)
[group: 'doc']
build-phpdoc: (_start "PHPDoc: API") && (_done "PHPDoc: API")
	#!/usr/bin/env zsh
	if [ -d .phpdoc ]; then
		echo "\tCleaning: cache..."
		rm -fr .phpdoc
	fi
	if [ ! -z doc/api ]; then
		echo "\tCleaning: api doc..."
		rm  doc/api
	fi

	phpdoc -d src -t .phpdoc --title="{{project}}" --defaultpackagename="Inane"
	ln -s $(realpath .phpdoc) doc/api

# compile asciidoc source into final files.
[group: 'doc']
build-asciidoc: (_start "Documentation") && (_done "Documentation")
    #!/usr/bin/env zsh
    echo build: doc: db

    echo building: changelog...
    asciidoctor-reducer -o CHANGELOG.adoc doc/changelog/index.adoc

    echo building: readme...
    asciidoctor-reducer -o README.adoc doc/readme/index.adoc
    echo doc: db: built

#endregion documentation
