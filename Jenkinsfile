pipeline {
    agent any

    tools {
        dockerTool 'Dockertool' 
    }

    environment {
        DOCKER_IMAGE = "payphone-app"
        CONTAINER_NAME = "payphone-container"
        PORT = "8081" 
    }

    stages {
        stage('Build') {
            steps {
                script {
                    echo 'Building Docker image...'
                    sh "docker build -t ${DOCKER_IMAGE} ."
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    echo 'Running tests...'
                    sh "docker run --rm ${DOCKER_IMAGE} find . -name '*.php' -exec php -l {} \\;"
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    echo 'Deploying application...'
                    sh "docker rm -f ${CONTAINER_NAME} || true"
                    sh "docker run -d --name ${CONTAINER_NAME} -p ${PORT}:80 ${DOCKER_IMAGE}"
                }
            }
        }
    }

    post {
        success {
            echo "Deployment successful! Accede en: http://localhost:${PORT}"
        }
        failure {
            echo 'Deployment failed. Revisa los logs de la consola.'
        }
    }
}