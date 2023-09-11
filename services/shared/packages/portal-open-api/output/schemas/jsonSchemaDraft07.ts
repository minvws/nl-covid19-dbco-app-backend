/**
 * Generated by orval v6.13.1 🍺
 * Do not edit manually.
 * DBCO Portal
 * API used for the portal for healthcare (BCO) workers
 * OpenAPI spec version: 1.0.0
 */
import type { NonNegativeInteger } from './nonNegativeInteger';
import type { NonNegativeIntegerDefault0 } from './nonNegativeIntegerDefault0';
import type { JsonSchemaDraft07AdditionalItems } from './jsonSchemaDraft07AdditionalItems';
import type { JsonSchemaDraft07Items } from './jsonSchemaDraft07Items';
import type { StringArray } from './stringArray';
import type { JsonSchemaDraft07AdditionalProperties } from './jsonSchemaDraft07AdditionalProperties';
import type { JsonSchemaDraft07Definitions } from './jsonSchemaDraft07Definitions';
import type { JsonSchemaDraft07Properties } from './jsonSchemaDraft07Properties';
import type { JsonSchemaDraft07PatternProperties } from './jsonSchemaDraft07PatternProperties';
import type { JsonSchemaDraft07Dependencies } from './jsonSchemaDraft07Dependencies';
import type { JsonSchemaDraft07Type } from './jsonSchemaDraft07Type';
import type { SchemaArray } from './schemaArray';

export interface JsonSchemaDraft07 {
  default?: unknown;
  const?: unknown;
  $id?: string;
  $schema?: string;
  $ref?: string;
  $comment?: string;
  title?: string;
  i18n?: string;
  description?: string;
  readOnly?: boolean;
  writeOnly?: boolean;
  examples?: unknown[];
  multipleOf?: number;
  maximum?: number;
  exclusiveMaximum?: number;
  minimum?: number;
  exclusiveMinimum?: number;
  maxLength?: NonNegativeInteger;
  minLength?: NonNegativeIntegerDefault0;
  pattern?: string;
  additionalItems?: JsonSchemaDraft07AdditionalItems;
  items?: JsonSchemaDraft07Items;
  maxItems?: NonNegativeInteger;
  minItems?: NonNegativeIntegerDefault0;
  uniqueItems?: boolean;
  contains?: JsonSchemaDraft07;
  maxProperties?: NonNegativeInteger;
  minProperties?: NonNegativeIntegerDefault0;
  required?: StringArray;
  additionalProperties?: JsonSchemaDraft07AdditionalProperties;
  definitions?: JsonSchemaDraft07Definitions;
  properties?: JsonSchemaDraft07Properties;
  patternProperties?: JsonSchemaDraft07PatternProperties;
  dependencies?: JsonSchemaDraft07Dependencies;
  propertyNames?: JsonSchemaDraft07;
  enum?: unknown[];
  type?: JsonSchemaDraft07Type;
  format?: string;
  contentMediaType?: string;
  contentEncoding?: string;
  if?: JsonSchemaDraft07;
  then?: JsonSchemaDraft07;
  else?: JsonSchemaDraft07;
  allOf?: SchemaArray;
  anyOf?: SchemaArray;
  oneOf?: SchemaArray;
  not?: JsonSchemaDraft07;
}
