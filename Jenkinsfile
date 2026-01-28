pipeline {
    agent any

    environment {
        DOCKER_IMAGE = "payphone-app"
        CONTAINER_NAME = "payphone-container"
        PORT = "8080"
    }

    stages {
        stage('Build') {
            steps {
                script {
                    echo 'Building Docker image...'
                    // Build the image from the Dockerfile in the current directory
                    sh "docker build -t ${DOCKER_IMAGE} ."
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    echo 'Deploying application...'
                    // Check if container exists and remove it (ignore error if not exists)
                    sh "docker rm -f ${CONTAINER_NAME} || true"
                    
                    // Run the new container
                    sh "docker run -d --name ${CONTAINER_NAME} -p ${PORT}:80 ${DOCKER_IMAGE}"
                }
            }
        }
    }

    post {
        success {
            echo 'Deployment successful!'
        }
        failure {
            echo 'Deployment failed.'
        }
    }
}
