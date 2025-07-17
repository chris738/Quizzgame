#!/bin/bash

# hostname.sh - Script to process hostnames and map them to types
# Bug fix: Ensure hostname remains unchanged when no mapping is found

# Define a map/dictionary of hostname patterns to types
declare -A hostname_map=(
    ["web"]="webserver"
    ["db"]="database" 
    ["app"]="application"
    ["test"]="testing"
    # Note: 'apdp' is intentionally not in the map to reproduce the bug scenario
)

# Function to process hostname
process_hostname() {
    local input_hostname="$1"
    local original_hostname="$input_hostname"
    local found_type="unknown"
    
    echo "Processing hostname: $input_hostname"
    
    # Search for patterns in the hostname
    for pattern in "${!hostname_map[@]}"; do
        if [[ "$input_hostname" == *"$pattern"* ]]; then
            found_type="${hostname_map[$pattern]}"
            echo "Found pattern '$pattern' -> Type: $found_type"
            echo "Output hostname: $input_hostname"
            return 0
        fi
    done
    
    # If no pattern found, return original hostname unchanged
    echo "No pattern found in map"
    echo "Type: $found_type"
    echo "Output hostname: $original_hostname"
    
    return 0
}

# Main script logic
if [ $# -eq 0 ]; then
    echo "Usage: $0 <hostname>"
    echo "Example: $0 lxapdptrntst01"
    exit 1
fi

# Process the provided hostname
process_hostname "$1"