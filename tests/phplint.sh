echo "start php syntax check";
result=$(find ../ -type f -name \*.php -exec php -l {} \; | grep -ci "Errors parsing ")
exit $result;