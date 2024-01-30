# Introduction

- [Getting started](./introduction/getting-started.md)
- [Building]()
- [Running tests]()
- [Debugging]()

# Project overview

- [Directory structure]()
- [Interpreter pipeline]()

# Core

- [Overview]()
  - [Startup/shutdown]()
- [Data structures]()
  - [zval]()
    - [zend_string]()
    - [zend_array]()
    - [zend_object]()
    - [Reference counting]()
  - [zend_function]()
  - [zend_class_entry]()
  - [zend_arena]()
- [Scanner]()
- [Parser]()
- [Compiler]()
  - [znode]()
  - [Comptime]()
  - [Delayed opcodes]()
- [Virtual machine]()
  - [Stack]()
  - [Basic opcodes]()
  - [zend_vm_def.h]()
  - [Execution]()
  - [Exceptions]()
  - [Function calls]()
  - [Specialization]()
  - [Cache slots]()
  - [Trampoline]()
  - [Opcode reference]()
- [Classes and objects]()
- [Writing internal functions]()
- [Zend alloc]()
- [Iterators]()
- [Cycle collector]()
- [Closures]()
- [Enums]()
- [Observers]()
- [ZTS]()

# SAPIs

- [Core concepts]()
- [FPM]()
- [CLI]()
  - [Builtin web server]()

# Extensions

- [Core concepts]()
- [Opcache]()
  - [Shared memory]()
  - [Opcode persistence]()
  - [Inheritance cache]()
  - [JIT]()
    - [Tracing]()
    - [IR]()
- [Streams]()
- [DOM]()

# Miscellaneous

- [Performance profiling]()
- [CI infrastructure]()
- [Serialization format]()
- [Old]()
  - [Input filter](./miscellaneous/old/input-filter.md)
  - [Mailing list rules](./miscellaneous/old/mailinglist-rules.md)
  - [Output API](./miscellaneous/old/output-api.md)
  - [Parameter parsing API](./miscellaneous/old/parameter-parsing-api.md)
  - [Release process](./miscellaneous/old/release-process.md)
  - [Self contained extensions](./miscellaneous/old/self-contained-extensions.md)
  - [Streams](./miscellaneous/old/streams.md)
  - [Unix build system](./miscellaneous/old/unix-build-system.md)
