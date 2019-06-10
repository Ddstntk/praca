import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import 'rxjs/add/operator/first';

import { AuthenticationService } from '../_services/authentication.service';

@Component({
    moduleId: module.id,
    templateUrl: 'login.component.html'
})

export class LoginComponent implements OnInit {
    model: any = {};
    loading = false;
    returnUrl: string;
    error = '';

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private authenticationService: AuthenticationService) { }

    ngOnInit() {
        // reset login status
        this.authenticationService.logout();

        // get return url from route parameters or default to '/'
        this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
    }

    login() {
        this.loading = true;
        var formData = new FormData();

        formData.append("login_type[email]", this.model.username);
        formData.append("login_type[password]",this.model.password);
        this.authenticationService.login(this.model.username, this.model.password)
            .first()
            .subscribe(
        // this.authenticationService.serverLogin(formData).subscribe(
            data => {
                // this.authenticationService.login(this.model.username, this.model.password)
                //     .first()
                //
                this.authenticationService.serverLogin(formData).subscribe(

                    data => {
                        // console.log(this.returnUrl)
                            this.router.navigate(['/dashboard']);
                        },
                        error => {
                            this.error = error;
                            this.loading = false;
                        });
            },
            error => {
                this.error = error;
                this.loading = false;
            });
        // this.authenticationService.login(this.model.username, this.model.password)
        //     .first()
        //     .subscribe(
        //         data => {
        //             this.router.navigate([this.returnUrl]);
        //         },
        //         error => {
        //             this.error = error;
        //             this.loading = false;
        //         });
        // {"Form data":{"login_type[email]":"admin@admin.com","login_type[password]":"admin123"}}

    }
}