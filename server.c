#include <stdio.h>
#include <pthread.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <string.h>
#include <stdlib.h>

#define NICKNAME_LENGTH 32
#define MAX_CONNECTIONS 100
#define BUFFER_LENGTH 1000
#define PORT 21996

typedef struct {
    pthread_t tid;
    char c_nickname[NICKNAME_LENGTH];
    int c_socket;
} client;

struct {
    client clients[MAX_CONNECTIONS];
    int length;
} peers;

void PeersInitialize(){
    peers.length = 0;
}

int PeersAdd(char* nickname, int socket){
    if (peers.length == MAX_CONNECTIONS) {
        return -1;
    }

    client p;
    strncpy(p.c_nickname, nickname, NICKNAME_LENGTH);
    p.c_socket = socket;

    peers.clients[peers.length++] = p;
    return 0;
}

void PeersDelete(char* nickname){
    int i = 0, j;
    while (i < peers.length) {
        if (strcmp(nickname, peers.clients[i].c_nickname) == 0) {
            for(j = i; j < peers.length - 1; j++) {
                peers.clients[j] = peers.clients[j + 1];
            }

            peers.length--;
        }
        else {
            i++;
        }
    }
}

int PeersGetLength(){
    return peers.length;
}

void PeersPrint(){
    int i = 0;
    for ( ; i < peers.length; i++){
        printf("Nickname: %s (%d)\n", peers.clients[i].c_nickname, peers.clients[i].c_socket);
    }
}

int CreateServerSocket(unsigned int ip, unsigned short port){
    int server_socket = socket(PF_INET, SOCK_STREAM, 0);

    if (server_socket == -1) {
        perror("create socket failed");
        return -1;
    }

    struct sockaddr_in addr = {
        AF_INET,
        htons(port),
        {
            htonl(ip)
        }
    };

    if (bind(server_socket, (struct sockaddr*)&addr, sizeof addr) == -1) {
        perror("bind failed");
        close(server_socket);
        return -1;
    }

    if (listen(server_socket, MAX_CONNECTIONS) == -1) {
        perror("listen failed");
        close(server_socket);
        return -1;
    }

    return server_socket;
}

char* GetLineFromSocket(int socket){
    char c;
    char* sp = (char*)malloc(BUFFER_LENGTH);
    int i = 0;

    while (recv(socket, &c, 1, 0) > 0) {
        if (c == '\n') {
            sp[i] = 0;
            return sp;
        }
        else {
            sp[i++] = c;
        }
    }

    return 0;
}

int SendLineToSocket(int socket, char* str){
    int length = strlen(str);
    str[length] = '\n';
    str[length + 1] = 0;

    return send(socket, str, length + 1, 0);
}

void* ClientCommunicationThread(void* args){
    client* clientinfo = (client*)args;
    int sbytes;
    char* rstr;

    char nickname_message[] = "Please type your nickname: ";
    sbytes = SendLineToSocket(clientinfo->.c_socket, nickname_message);
    if (sbytes != strlen(nickname_message)) {
        perror("send failed");
        pthread_cancel(clientinfo->tid);
    }

    rstr = GetLineFromSocket(clientinfo->c_socket);

    if(rstr == 0){
        perror("get line failed");
        pthread_cancel(clientinfo->tid);
    }
    else {
        strncpy(clientinfo->c_nickname, rstr, NICKNAME_LENGTH);
    }

    return 0;
}

int main(){
    int server_socket = CreateServerSocket(INADDR_ANY, PORT);
    int client_socket;
    pthread_t tid;
    client* clientinfo;

    PeersInitialize();

    if (server_socket == -1) {
        printf("closing...");
        fflush(stdout);
        return 0;
    }

    while (client_socket = accept(server_socket, 0, 0), client_socket != -1) {
        PeersAdd("", client_socket);
        pthread_create(&tid, 0, ClientCommunicationThread, peers.clients + peers.length - 1);
        peers.clients[peers.length - 1].tid = tid;
        printf("%d connections\n", PeersGetLength());
    }

    printf("closing...");
    close(server_socket);

    return 0;
}
