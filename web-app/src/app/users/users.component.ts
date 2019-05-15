import { Component, OnInit } from '@angular/core';
import { UsersComponentService } from "./users.component.service";

@Component({
  selector: 'app-users',
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.scss'],
  providers: [UsersComponentService]
})
export class UsersComponent implements OnInit {
  friends: any;

  constructor(private userService: UsersComponentService) { }

  ngOnInit() {
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
  }

  indexAction() {
    this.userService.indexAction().subscribe(data => {

    });
  }

  profileAction() {
      this.userService.profileAction().subscribe(data => {

      });
    }

  viewAction() {
    this.userService.viewAction(1234).subscribe(data => {

    });
  }

  editAction() {
    this.userService.editAction({}).subscribe(data => {

    });
  }

  passwordAction() {
    this.userService.passwordAction({password: "admin123"}).subscribe(data => {

    });
  }
}
