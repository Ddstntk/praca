import { Component, OnInit } from '@angular/core';
import { FriendsComponentService } from "./friends.component.service";

@Component({
  selector: 'app-friends',
  templateUrl: './friends.component.html',
  styleUrls: ['./friends.component.scss'],
  providers: [FriendsComponentService]
})
export class FriendsComponent implements OnInit {
  friends: any;

  constructor(private friendsService: FriendsComponentService) { }

  ngOnInit() {
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
  }

  indexAction() {
    this.friendsService.indexAction().subscribe(data => {

    });
  }

  indexInvitesAction() {
      this.friendsService.indexInvitesAction().subscribe(data => {

      });
    }

  inviteAction() {
    this.friendsService.inviteAction(1234).subscribe(data => {

    });
  }

  addFriendAction() {
    this.friendsService.addFriendAction(1234).subscribe(data => {

    });
  }

  deleteFriendAction() {
    this.friendsService.deleteAction(1234).subscribe(data => {

    });
  }

  login() {
    this.friendsService.loginAction({_username: "admin@admin.com", _password: "admin123"}).subscribe(data => {

    });
  }
}
