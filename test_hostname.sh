#!/bin/bash

# test_hostname.sh - Test script to verify hostname.sh fixes the bug
# This test demonstrates that the bug described in the issue is resolved

echo "Testing hostname.sh script..."
echo "=============================="

# Test case from the problem statement
echo "Test 1: Input 'lxapdptrntst01' (apdp not in map)"
result=$(./hostname.sh lxapdptrntst01 | grep "Output hostname:" | cut -d':' -f2 | tr -d ' ')

if [ "$result" = "lxapdptrntst01" ]; then
    echo "✓ PASS: Hostname remains unchanged (expected: lxapdptrntst01, got: $result)"
else
    echo "✗ FAIL: Hostname was corrupted (expected: lxapdptrntst01, got: $result)"
    echo "       This would be the bug: lxxapdpttrntst01"
fi

echo ""

# Additional test cases
echo "Test 2: Input 'webserver01' (web in map)"
result=$(./hostname.sh webserver01 | grep "Output hostname:" | cut -d':' -f2 | tr -d ' ')
echo "Result: $result (should be webserver01)"

echo ""

echo "Test 3: Input 'randomhost' (not in map)"
result=$(./hostname.sh randomhost | grep "Output hostname:" | cut -d':' -f2 | tr -d ' ')
if [ "$result" = "randomhost" ]; then
    echo "✓ PASS: Hostname remains unchanged for unknown patterns"
else
    echo "✗ FAIL: Hostname was corrupted for unknown patterns"
fi

echo ""
echo "All tests completed!"