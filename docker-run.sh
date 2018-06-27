#!/bin/bash
docker run -d -p 8081:80 -p 8083:8083 -p 27017:27017 -p 222:22 -p 54322:5432 --name vitaweb -v $(pwd):/var/www vitaweb

