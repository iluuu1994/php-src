/* This is a generated file, edit compact_vector.stub.php instead.
 * Stub hash: d9442bc4ab0adb284daf26b5624ac186d21a0d2b */

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_CompactVector___construct, 0, 0, 1)
	ZEND_ARG_TYPE_INFO(0, type, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_CompactVector_offsetExists, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_CompactVector_offsetGet, 0, 1, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_CompactVector_offsetSet, 0, 2, IS_VOID, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO(0, value, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_CompactVector_offsetUnset, 0, 1, IS_VOID, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_METHOD(CompactVector, __construct);
ZEND_METHOD(CompactVector, offsetExists);
ZEND_METHOD(CompactVector, offsetGet);
ZEND_METHOD(CompactVector, offsetSet);
ZEND_METHOD(CompactVector, offsetUnset);

static const zend_function_entry class_CompactVector_methods[] = {
	ZEND_ME(CompactVector, __construct, arginfo_class_CompactVector___construct, ZEND_ACC_PUBLIC)
	ZEND_ME(CompactVector, offsetExists, arginfo_class_CompactVector_offsetExists, ZEND_ACC_PUBLIC)
	ZEND_ME(CompactVector, offsetGet, arginfo_class_CompactVector_offsetGet, ZEND_ACC_PUBLIC)
	ZEND_ME(CompactVector, offsetSet, arginfo_class_CompactVector_offsetSet, ZEND_ACC_PUBLIC)
	ZEND_ME(CompactVector, offsetUnset, arginfo_class_CompactVector_offsetUnset, ZEND_ACC_PUBLIC)
	ZEND_FE_END
};

static zend_class_entry *register_class_CompactVector(zend_class_entry *class_entry_ArrayAccess)
{
	zend_class_entry ce, *class_entry;

	INIT_CLASS_ENTRY(ce, "CompactVector", class_CompactVector_methods);
	class_entry = zend_register_internal_class_with_flags(&ce, NULL, ZEND_ACC_FINAL|ZEND_ACC_NOT_SERIALIZABLE);
	zend_class_implements(class_entry, 1, class_entry_ArrayAccess);

	return class_entry;
}
