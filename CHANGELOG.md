Version: $Id$ ($Date$)

# History

## 0.3.0-dev (2025 Xxx xx)

- update: `AbstractEntity::save` added optional parameter **bool**
  `insert` for non-automatic primary id fields.

- update: `AbstractTable` fetch single entity by id returns result
  correctly.

- new: `AbstractTable::insertUpdate` uses
  `insert on conflict update syntax`.

- new: `EntityPrepareMethod` method attribute indicates method to call
  pre db persistence.

- update: moved to new docs structure.

- update: `OptionsInterface` used throughout.

## 0.2.0 (2025 Apr 25)

- new: DB-Layer for database access initial basic release

## 0.1.0 (2022 Jul 22)

- initial release
