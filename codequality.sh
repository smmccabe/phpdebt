#!/bin/bash

FAULTS=0
FOLDER=$1

FAULTS=$((FAULTS + $(phpmd $FOLDER text cleancode | wc -l)))
FAULTS=$((FAULTS + $(phpmd $FOLDER text codesize | wc -l)))
FAULTS=$((FAULTS + $(phpmd $FOLDER text controversial | wc -l)))
FAULTS=$((FAULTS + $(phpmd $FOLDER text design | wc -l)))
FAULTS=$((FAULTS + $(phpmd $FOLDER text naming | wc -l)))
FAULTS=$((FAULTS + $(phpmd $FOLDER text unusedcode | wc -l)))

TOTAL=$(cloc $FOLDER --md | sed -n -r 's/SUM:\|.*\|(.*)/\1/p')
PERCENT=$(echo "scale=2; ($FAULTS / $TOTAL) * 100" | bc | sed -n -r 's/(.*)\..*/\1/p')

echo "Total Faults: $FAULTS
Total Lines: $TOTAL
Quality Score: $PERCENT"
