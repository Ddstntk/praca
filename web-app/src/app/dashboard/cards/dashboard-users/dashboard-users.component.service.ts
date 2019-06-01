import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class UsersComponentService {

    constructor(
        private http: HttpClient) { }

    inviteAction(friendId): Observable<any> {
        return this.http.get('api/friend/invite/' + friendId);
    }

    addFriendAction(friendId): Observable<any> {
        return this.http.get('api/friend/add/' + friendId);
    }

    indexAction(filter, page): Observable<any> {
        return this.http.post('api/user/index', {filter:filter, page:page});
    }

    indexInvitesAction(): Observable<any> {
        return this.http.get('api/friend/invites');
    }

    deleteAction(friendId): Observable<any> {
        return this.http.get('api/friend/'+ friendId +'/delete');
    }

    getIdAction(): Observable<any> {
        return this.http.get('api/user/id');
    }

    loginAction(friendId): Observable<any> {
        return this.http.post('auth/login', friendId);
    }

    getConfigAction(): Observable<any> {
        return this.http.get('api/user/config');
    }
}

// $controller->get('/invite/{friendId}', [$this, 'apiInviteAction']);
// $controller->get('/add/{friendId}', [$this, 'apiAddFriend']);
// $controller->get('/index', [$this, 'apiIndexAction']);
// $controller->get('/invites', [$this, 'apiIndexInvites']);
// $controller->match('/{id}/delete', [$this, 'apiDeleteActi