/**
 * Generated by orval v6.13.1 🍺
 * Do not edit manually.
 * DBCO Portal
 * API used for the portal for healthcare (BCO) workers
 * OpenAPI spec version: 1.0.0
 */
import type { FormMetaDataLinks } from './formMetaDataLinks';
import type { FormValidationError } from './formValidationError';

export interface FormMetaData {
  /** An object with links to that is used by the form to perform CRUD actions */
  $links: FormMetaDataLinks;
  $validationErrors?: FormValidationError[];
}
