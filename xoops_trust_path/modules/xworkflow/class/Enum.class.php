<?php

/**
 * enumerate.
 */
class Xworkflow_Result
{
    const HOLD = 0;
    const REJECT = 1;
    const APPROVE = 9;
}

class Xworkflow_RevertTo
{
    const ZERO = 0;   // revert to the poster
    const FORMER = 1; // revert to the former approval
}
