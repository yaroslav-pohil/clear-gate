FROM node:20.15-alpine

WORKDIR /var/www

# Make port 5173 available outside the container
EXPOSE 5173

ENV HOST=0.0.0.0
ENV PORT=5173

# Install dependencies and start the development server
CMD ["/bin/sh", "-c", "npm install && npm run dev -- --host 0.0.0.0"] 