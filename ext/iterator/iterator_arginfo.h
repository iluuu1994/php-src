/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: a88cef38e74967a406ac77ec1066f91406ad2e6a */

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_Iterator_range, 0, 2, Iterator\\RangeIterator, 0)
	ZEND_ARG_TYPE_INFO(0, from, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, to, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_Iterator_RangeIterator___construct, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Iterator_RangeIterator_current, 0, 0, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Iterator_RangeIterator_next, 0, 0, IS_VOID, 0)
ZEND_END_ARG_INFO()

#define arginfo_class_Iterator_RangeIterator_key arginfo_class_Iterator_RangeIterator_current

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Iterator_RangeIterator_valid, 0, 0, _IS_BOOL, 0)
ZEND_END_ARG_INFO()

#define arginfo_class_Iterator_RangeIterator_rewind arginfo_class_Iterator_RangeIterator_next

ZEND_FUNCTION(Iterator_range);
ZEND_METHOD(Iterator_RangeIterator, __construct);
ZEND_METHOD(Iterator_RangeIterator, current);
ZEND_METHOD(Iterator_RangeIterator, next);
ZEND_METHOD(Iterator_RangeIterator, key);
ZEND_METHOD(Iterator_RangeIterator, valid);
ZEND_METHOD(Iterator_RangeIterator, rewind);

static const zend_function_entry ext_functions[] = {
	ZEND_RAW_FENTRY(ZEND_NS_NAME("Iterator", "range"), zif_Iterator_range, arginfo_Iterator_range, 0, NULL, NULL)
	ZEND_FE_END
};

static const zend_function_entry class_Iterator_RangeIterator_methods[] = {
	ZEND_ME(Iterator_RangeIterator, __construct, arginfo_class_Iterator_RangeIterator___construct, ZEND_ACC_PRIVATE)
	ZEND_ME(Iterator_RangeIterator, current, arginfo_class_Iterator_RangeIterator_current, ZEND_ACC_PUBLIC)
	ZEND_ME(Iterator_RangeIterator, next, arginfo_class_Iterator_RangeIterator_next, ZEND_ACC_PUBLIC)
	ZEND_ME(Iterator_RangeIterator, key, arginfo_class_Iterator_RangeIterator_key, ZEND_ACC_PUBLIC)
	ZEND_ME(Iterator_RangeIterator, valid, arginfo_class_Iterator_RangeIterator_valid, ZEND_ACC_PUBLIC)
	ZEND_ME(Iterator_RangeIterator, rewind, arginfo_class_Iterator_RangeIterator_rewind, ZEND_ACC_PUBLIC)
	ZEND_FE_END
};

static zend_class_entry *register_class_Iterator_RangeIterator(zend_class_entry *class_entry_Iterator)
{
	zend_class_entry ce, *class_entry;

	INIT_NS_CLASS_ENTRY(ce, "Iterator", "RangeIterator", class_Iterator_RangeIterator_methods);
	class_entry = zend_register_internal_class_with_flags(&ce, NULL, ZEND_ACC_FINAL);
	zend_class_implements(class_entry, 1, class_entry_Iterator);

	zval property_from_default_value;
	ZVAL_UNDEF(&property_from_default_value);
	zend_declare_typed_property(class_entry, ZSTR_KNOWN(ZEND_STR_FROM), &property_from_default_value, ZEND_ACC_PUBLIC|ZEND_ACC_READONLY, NULL, (zend_type) ZEND_TYPE_INIT_MASK(MAY_BE_LONG));

	zval property_to_default_value;
	ZVAL_UNDEF(&property_to_default_value);
	zend_string *property_to_name = zend_string_init("to", sizeof("to") - 1, 1);
	zend_declare_typed_property(class_entry, property_to_name, &property_to_default_value, ZEND_ACC_PUBLIC|ZEND_ACC_READONLY, NULL, (zend_type) ZEND_TYPE_INIT_MASK(MAY_BE_LONG));
	zend_string_release(property_to_name);

	zval property_key_default_value;
	ZVAL_UNDEF(&property_key_default_value);
	zend_declare_typed_property(class_entry, ZSTR_KNOWN(ZEND_STR_KEY), &property_key_default_value, ZEND_ACC_PUBLIC, NULL, (zend_type) ZEND_TYPE_INIT_MASK(MAY_BE_LONG));

	zval property_current_default_value;
	ZVAL_UNDEF(&property_current_default_value);
	zend_string *property_current_name = zend_string_init("current", sizeof("current") - 1, 1);
	zend_declare_typed_property(class_entry, property_current_name, &property_current_default_value, ZEND_ACC_PUBLIC, NULL, (zend_type) ZEND_TYPE_INIT_MASK(MAY_BE_LONG));
	zend_string_release(property_current_name);

	return class_entry;
}
