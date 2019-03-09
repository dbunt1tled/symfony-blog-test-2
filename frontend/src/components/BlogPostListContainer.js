import React from 'react';
import BlogPostList from "./BlogPostList";

class BlogPostListContainer extends React.Component {

    constructor(props) {
        super(props);
        this.posts = [
            {
                id: 1,
                title: 'Hello My First Record '
            },
            {
                id: 2,
                title: 'Hello My Second Record '
            },
        ]
    }
    render() {
        return (
            <div>
                <BlogPostList posts={this.posts} id={5}/>
        </div>);
    }
}

export default BlogPostListContainer;