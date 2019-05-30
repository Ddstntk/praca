import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class ConfigComponentService {

    constructor(
        private http: HttpClient) { }

    setConfigAction(config): Observable<any> {
        return this.http.post('api/user/config/set', {body: config});
    }

    getConfigAction(): Observable<any> {
        return this.http.get('api/user/config');
    }
}
;