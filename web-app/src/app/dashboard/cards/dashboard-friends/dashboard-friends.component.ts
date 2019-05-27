import {Component, Injector, OnInit} from '@angular/core';
import {DashboardCard} from '../dashboard-card';
import {AbstractDashboardCard} from '../abstract-dashboard-card';
import {FriendsComponentService} from "./dashboard-friends.component.service";
// import {ChatComponentService} from "../../../chat/chat.component.service";
// import {UsersComponentService} from "../../../users/users.component.service";
// import {PostsComponentService} from "./dashboard-posts.component.service";


@Component({
  selector: 'app-dashboard-friends',
  templateUrl: './dashboard-friends.component.html',
  styleUrls: ['./dashboard-friends.component.scss']
})
export class DashboardFriendsComponent extends AbstractDashboardCard implements OnInit {

  constructor(private injector: Injector, private friendsService: FriendsComponentService) {
    super(injector.get(DashboardCard.metadata.NAME),
      injector.get(DashboardCard.metadata.ROUTERLINK),
      injector.get(DashboardCard.metadata.ICONCLASS),
      injector.get(DashboardCard.metadata.COLS),
      injector.get(DashboardCard.metadata.ROWS),
      injector.get(DashboardCard.metadata.COLOR));
  }
  friends: any;
  view: any;

  ngOnInit() {
    this.indexAction();
    console.log(JSON.stringify(this.friends))
    // alert(JSON.stringify(this.friends));
  }

  indexAction() {
    this.friendsService.indexAction().subscribe(data => {
      this.friends = data.friendsIndexed;
      this.view = "index";
    });
  }

  messageAction(userId) {
    this.friendsService.messageAction({messaged:userId}).subscribe(data => {
    });
  }

  indexInvitesAction() {
    this.friendsService.indexInvitesAction().subscribe(data => {

    });
  }
  //
  // inviteAction() {
  //   this.friendsService.inviteAction(1234).subscribe(data => {
  //
  //   });
  // }

  invitesAction() {
    this.friendsService.indexInvitesAction().subscribe(data => {
      this.friends = data.friendsIndexed.data;
      this.view = "invites";
    });
  }


  addFriendAction(id) {
    this.friendsService.addFriendAction(id).subscribe(data => {
      this.indexAction();
    });
  }

  deleteFriendAction() {
    this.friendsService.deleteAction(1234).subscribe(data => {

    });
  }

}