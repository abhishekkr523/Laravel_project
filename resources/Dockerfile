# Use official Node.js image as a base
FROM node:latest

# Set working directory
WORKDIR /app

# Copy the package.json and install dependencies
COPY package*.json ./
RUN npm install

# Copy the rest of your application code
COPY . .

# Build the Vue.js app
RUN npm run build

# Expose the port that the app will run on
EXPOSE 8080

# Serve the app
CMD ["npm", "run", "serve"]
