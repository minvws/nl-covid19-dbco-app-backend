import type { CallQueue, CurrentSection } from '../../sectionManagementTypes';

import getPreviousTargetsToBeMerged from './getPreviousTargetsToBeMerged';

const mergeQueue: CallQueue['mergeQueue'] = [
    {
        target: '123',
        payload: ['234', '345'],
    },
    {
        target: {
            label: 'Test1',
            uuid: '456',
            indexCount: 0,
        },
        payload: ['567', '678'],
    },
    {
        target: '789',
        payload: ['891', '198'],
    },
];

const mergeSections: CurrentSection[] = [
    {
        label: 'Test1',
        indexCount: 0,
        uuid: '456',
    },
    {
        label: 'Test4',
        indexCount: 0,
        uuid: '789',
    },
    {
        label: 'Test5',
        indexCount: 0,
        uuid: '5',
    },
];

describe('getPreviousTargetsToBeMerged', () => {
    it('should return previous merge targets that are now to be merged into another', () => {
        expect(getPreviousTargetsToBeMerged(mergeSections, mergeQueue)).toStrictEqual([
            {
                target: {
                    label: 'Test1',
                    uuid: '456',
                    indexCount: 0,
                },
                payload: ['567', '678'],
            },
            {
                target: '789',
                payload: ['891', '198'],
            },
        ]);
    });
});
