import {Component, Injector, OnInit} from '@angular/core';
import {DashboardCard} from '../dashboard-card';
import {AbstractDashboardCard} from '../abstract-dashboard-card';
import {UsersComponentService} from "./dashboard-users.component.service";
// import {ChatComponentService} from "../../../chat/chat.component.service";
// import {UsersComponentService} from "../../../users/users.component.service";
// import {PostsComponentService} from "./dashboard-posts.component.service";


@Component({
  selector: 'app-dashboard-users',
  templateUrl: './dashboard-users.component.html',
  styleUrls: ['./dashboard-users.component.scss']
})
export class DashboardUsersComponent extends AbstractDashboardCard implements OnInit {

  constructor(private injector: Injector, private usersService: UsersComponentService) {
    super(injector.get(DashboardCard.metadata.NAME),
      injector.get(DashboardCard.metadata.ROUTERLINK),
      injector.get(DashboardCard.metadata.ICONCLASS),
      injector.get(DashboardCard.metadata.COLS),
      injector.get(DashboardCard.metadata.ROWS),
      injector.get(DashboardCard.metadata.COLOR));
  }
  users: any;
  pagesNumber: number;
  filter: any;

  ngOnInit() {
    this.indexAction();
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
  }

  indexAction(filter = false) {
    this.usersService.indexAction(filter).subscribe(data => {
      this.users = data.usersIndexed.data;
      console.log(this.users)
      this.pagesNumber = data.usersIndexed.pages_number
    });
  }

  getFiltered() {
    this.indexAction(this.filter);
    console.log(this.users)
  }

  inviteAction(id) {
    this.usersService.inviteAction(id).subscribe(data => {
    });
  }
  //
  // profileAction() {
  //   this.usersService.profileAction().subscribe(data => {
  //
  //   });
  // }
  //
  // viewAction() {
  //   this.usersService.viewAction(1234).subscribe(data => {
  //
  //   });
  // }
  //
  // editAction() {
  //   this.usersService.editAction({}).subscribe(data => {
  //
  //   });
  // }
  //
  // passwordAction() {
  //   this.usersService.passwordAction({password: "admin123"}).subscribe(data => {
  //
  //   });
  // }

}