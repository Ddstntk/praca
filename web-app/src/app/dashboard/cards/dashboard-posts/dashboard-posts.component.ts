import {Component, Injector, OnInit} from '@angular/core';
import {DashboardCard} from '../dashboard-card';
import {AbstractDashboardCard} from '../abstract-dashboard-card';
// import {ChatComponentService} from "../../../chat/chat.component.service";
// import {UsersComponentService} from "../../../users/users.component.service";
import {PostsComponentService} from "./dashboard-posts.component.service";


@Component({
  selector: 'app-dashboard-posts',
  templateUrl: './dashboard-posts.component.html',
  styleUrls: ['./dashboard-posts.component.scss']
})
export class DashboardPostsComponent extends AbstractDashboardCard implements OnInit {

  constructor(private injector: Injector, private postsService: PostsComponentService) {
    super(injector.get(DashboardCard.metadata.NAME),
      injector.get(DashboardCard.metadata.ROUTERLINK),
      injector.get(DashboardCard.metadata.ICONCLASS),
      injector.get(DashboardCard.metadata.COLS),
      injector.get(DashboardCard.metadata.ROWS),
      injector.get(DashboardCard.metadata.COLOR));
  }
  friends: any;
  chats = [];
  chatSelected: number;
  posts: any;
  page: number;

  ngOnInit() {
    this.page = 1;
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
    // this.chatSelected=81;
    // this.chatService.allAction().subscribe(data => {
    //   this.chats = data.chatsIndexed[0];
    //   this.indexAction(this.chatSelected);
    // });
    this.indexAction()
  }

  indexAction() {
    this.postsService.indexAction(this.page).subscribe(data => {
      this.posts=data.postsIndexed.data;
    });
  }

  allAction() {
    // this.chatService.allAction().subscribe(data => {
    //
    // });
  }

  pageAction(value) {
    this.page += value;
    this.indexAction()
  }

  addAction(postBody) {
    this.postsService.addAction({body:postBody, visibility:0}).subscribe(data => {
      this.indexAction()
    });
  }

  selectChat(value) {
    // this.chatSelected = value;
  }

  idAction() {
    // this.userService.idAction().subscribe(data => {
    //   alert(data.id)
    //   return data
    // })
  }
}