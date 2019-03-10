import React from 'react';
import BlogPostList from "./BlogPostList";
import {blogPostList, blogPostListFetch} from "../actions/actions";
import {connect} from "react-redux";
import {requests} from "../agent";

const mapStateToProps = state => ({
    ...state.blogPostList
});

const mapDispatchToProps = {
  blogPostList,
  blogPostListFetch
};

class BlogPostListContainer extends React.Component {
    componentDidMount() {
        this.props.blogPostListFetch();
    }

    render() {
        return (
            <div>
                <BlogPostList posts={this.props.posts} />
        </div>);
    }
}

//export default BlogPostListContainer;
export default connect(mapStateToProps, mapDispatchToProps)(BlogPostListContainer);