import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class FriendsComponentService {

    constructor(
        private http: HttpClient) { }

    inviteAction(friendId): Observable<any> {
        return this.http.get('api/friend/invite/' + friendId);
    }

    addFriendAction(friendId): Observable<any> {
        return this.http.get('api/friend/add/' + friendId);
    }

    indexAction(): Observable<any> {
        return this.http.get('api/friend/index');
    }

    indexInvitesAction(): Observable<any> {
        return this.http.get('api/friend/invites');
    }

    deleteAction(friendId): Observable<any> {
        return this.http.get('api/friend/'+ friendId +'/delete');
    }

    messageAction(userId): Observable<any> {
        return this.http.post('api/chat/new', userId);
    }

    loginAction(friendId): Observable<any> {
        return this.http.post('auth/login', friendId);
    }
}

// $controller->get('/invite/{friendId}', [$this, 'apiInviteAction']);
// $controller->get('/add/{friendId}', [$this, 'apiAddFriend']);
// $controller->get('/index', [$this, 'apiIndexAction']);
// $controller->get('/invites', [$this, 'apiIndexInvites']);
// $controller->match('/{id}/delete', [$this, 'apiDeleteActi