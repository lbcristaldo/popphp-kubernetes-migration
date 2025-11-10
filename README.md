cat > README-KUBERNETES.md << 'EOF'
Pop PHP Legacy - Migración a Kubernetes

 Proyecto Original
Este es el framework Pop PHP v1 (legacy, 2016) migrado a contenedores y Kubernetes.

Documentación original:
- `README.md` - Documentación del framework original
- `INSTALL.md` - Instrucciones de instalación originales
- `CHANGELOG.md` - Historial de cambios del framework

 Migración a Docker/Kubernetes
 
 Arquitectura del Sistema

 Diagrama de Infraestructura
```mermaid
graph TB
    subgraph Local["Máquina Local"]
        Browser[Browser/curl<br/>localhost:8888]
    end
    
    subgraph Minikube["Kubernetes Cluster - Minikube"]
        Ingress[Ingress Controller<br/>popphp.local]
        Service[Service<br/>popphp-service<br/>ClusterIP:80]
        
        subgraph Deployment["Deployment: popphp-deployment"]
            Pod1[🐳 Pod 1<br/>Apache 2.4<br/>PHP 5.6]
            Pod2[🐳 Pod 2<br/>Apache 2.4<br/>PHP 5.6]
            Pod3[🐳 Pod 3<br/>Apache 2.4<br/>PHP 5.6]
        end
    end
    
    subgraph Registry["🐋 Docker Hub"]
        Image[lbcristaldo/popphp-legacy:latest]
    end
    
    Browser -->|port-forward| Service
    Browser -->|HTTP| Ingress
    Ingress --> Service
    Service -->|Load Balance| Pod1
    Service -->|Load Balance| Pod2
    Service -->|Load Balance| Pod3
    Image -.->|pull| Pod1
    Image -.->|pull| Pod2
    Image -.->|pull| Pod3
    
    style Pod1 fill:#326CE5,color:#fff,stroke:#fff,stroke-width:2px
    style Pod2 fill:#326CE5,color:#fff,stroke:#fff,stroke-width:2px
    style Pod3 fill:#326CE5,color:#fff,stroke:#fff,stroke-width:2px
    style Service fill:#0078D4,color:#fff,stroke:#fff,stroke-width:2px
    style Ingress fill:#FF6B6B,color:#fff,stroke:#fff,stroke-width:2px
    style Image fill:#2496ED,color:#fff,stroke:#fff,stroke-width:2px
```

 Flujo de Deployment
```mermaid
sequenceDiagram
    autonumber
    participant Dev as Developer
    participant Local as Docker Local
    participant Hub as 🐋 Docker Hub
    participant K8s as Kubernetes
    participant Pod as 🐳 Pod
    
    Dev->>Local: docker build -t popphp:latest
    Local-->>Dev: ✅ Build successful
    Dev->>Hub: docker push popphp:latest
    Hub-->>Dev: ✅ Image pushed
    Dev->>K8s: kubectl apply -f k8s/
    K8s->>Hub: Pull image
    Hub-->>K8s:  Image downloaded
    K8s->>Pod: Create pod with image
    Pod->>Pod: Start Apache
    Pod->>Pod: Load PHP 5.6
    Pod->>Pod: Mount /var/www/html
    Pod-->>K8s: ✅ Pod ready
    K8s-->>Dev: 🎉 Deployment successful
```

 Componentes de Kubernetes
```mermaid
graph LR
    subgraph Resources["Kubernetes Resources"]
        D[Deployment<br/>popphp-deployment<br/>replicas: 3]
        S[Service<br/>popphp-service<br/>type: ClusterIP]
        I[Ingress<br/>popphp.local<br/>path: /]
    end
    
    D --> P1[Pod 1]
    D --> P2[Pod 2]
    D --> P3[Pod 3]
    S --> P1
    S --> P2
    S --> P3
    I --> S
    
    style D fill:#4CAF50,color:#fff
    style S fill:#2196F3,color:#fff
    style I fill:#FF9800,color:#fff
    style P1 fill:#9C27B0,color:#fff
    style P2 fill:#9C27B0,color:#fff
    style P3 fill:#9C27B0,color:#fff
```

 Estados del Pod
```mermaid
stateDiagram-v2
    [*] --> Pending: kubectl apply
    Pending --> ContainerCreating: Image pull
    ContainerCreating --> Running: Container started
    Running --> Running: Health checks passing
    Running --> Terminating: kubectl delete
    Running --> CrashLoopBackOff: Container error
    CrashLoopBackOff --> Running: Auto restart
    Terminating --> [*]: Pod deleted
    
    note right of Running
        Apache listening on :80
        PHP 5.6 ready
        Serving requests
    end note
```
Estructura del proyecto
```
popphp-v1-legacy/
├── Dockerfile              # ← NUEVO: Imagen Docker
├── docker-compose.yml      # ← NUEVO: Compose (opcional)
├── .dockerignore          # ← NUEVO: Exclusiones de build
├── k8s/                   # ← NUEVO: Manifiestos Kubernetes
│   ├── deployment.yaml
│   ├── service.yaml
│   └── ingress.yaml
├── index.php              # Punto de entrada de la app
├── public/                # Assets del framework
├── vendor/                # Framework Pop PHP
└── script/                # Scripts CLI del framework
```

Guía de Despliegue

 Pre-requisitos
- Docker instalado
- Minikube instalado
- kubectl configurado

1. Build de la imagen
```bash
docker build -t lbcristaldo/popphp-legacy:latest .
```

2. Test local
```bash
docker run --rm -p 8080:80 lbcristaldo/popphp-legacy:latest
curl http://localhost:8080
```

3. Push a Docker Hub
```bash
docker push lbcristaldo/popphp-legacy:latest
```

4. Deploy en Kubernetes
```bash
 Iniciar Minikube
minikube start
minikube addons enable ingress

 Aplicar manifiestos
kubectl apply -f k8s/deployment.yaml
kubectl apply -f k8s/service.yaml
kubectl apply -f k8s/ingress.yaml

 - Verificar
kubectl get pods
kubectl get services
```

5. Acceder a la aplicación

Opción A: Port Forward
```bash
kubectl port-forward service/popphp-service 8888:80
 - Abrir: http://localhost:8888
```

Opción B: Ingress
```bash
 - Agregar a /etc/hosts
echo "$(minikube ip) popphp.local" | sudo tee -a /etc/hosts
 - Abrir: http://popphp.local
```

Troubleshooting

Ver logs del pod
```bash
kubectl logs -f deployment/popphp-deployment
```

Entrar al contenedor
```bash
POD=$(kubectl get pods -l app=popphp -o jsonpath='{.items[0].metadata.name}')
kubectl exec -it $POD -- bash
```

Rebuild y redeploy
```bash
docker build -t lbcristaldo/popphp-legacy:latest .
docker push lbcristaldo/popphp-legacy:latest
kubectl rollout restart deployment/popphp-deployment
```

Stack Tecnológico
- Framework: Pop PHP v1 (2016)
- Runtime: PHP 5.6 + Apache 2.4
- Containerización: Docker
- Orquestación: Kubernetes (Minikube)
- Registry: Docker Hub

Autor de la migración
Luciana Cristaldo - Noviembre 2025

Licencias
- Framework Pop PHP: Ver `LICENSE.txt`
- Migración a K8s: Proyecto académico
EOF
```
