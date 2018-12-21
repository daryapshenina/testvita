#!/bin/bash
docker run -d -p 8085:80 -p 8083:8083  -p 223:22 -p 54322:5432 --name vitaweb -v $(pwd):/var/www vitaweb

