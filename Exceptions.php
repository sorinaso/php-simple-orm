<?php
class SimpleORMException extends Exception {}
class DBError extends SimpleORMException {}
class NotImplementedError extends SimpleORMException {}
class ArgumentError extends SimpleORMException {}
class DataConsistencyError extends SimpleORMException {}
class ValidationError extends Exception {}
?>
