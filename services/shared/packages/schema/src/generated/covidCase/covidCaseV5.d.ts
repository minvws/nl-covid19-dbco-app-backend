/**
 * *** WARNING ***
 * This code is auto-generated. Any changes will be reverted by generating the schema!
 */

import { DTO } from '@dbco/schema/dto';
import { CovidCaseCommon } from './covidCaseCommon';
import { CovidCaseV1UpTo6 } from './covidCaseV1UpTo6';
import { CovidCaseV2Up } from './covidCaseV2Up';
import { CovidCaseV3Up } from './covidCaseV3Up';
import { CovidCaseV4Up } from './covidCaseV4Up';
import { CovidCaseV5Up } from './covidCaseV5Up';
import { CovidCaseV5UpTo5 } from './covidCaseV5UpTo5';

export type CovidCaseV5 = CovidCaseCommon & CovidCaseV1UpTo6 & CovidCaseV2Up & CovidCaseV3Up & CovidCaseV4Up & CovidCaseV5Up & CovidCaseV5UpTo5;

export type CovidCaseV5DTO = DTO<CovidCaseV5>;
