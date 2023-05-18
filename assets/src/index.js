import {default as metadata} from './block.json';
import {registerBlockType} from '@wordpress/blocks';
import {useBlockProps} from '@wordpress/block-editor';
import {TextControl} from '@wordpress/components';

registerBlockType(metadata, {
  edit: ({attributes, setAttributes}) => {
    const blockProps = useBlockProps({
      className: 'dashifen-movie-block-container'
    });

    return (
      <div {...blockProps}>
        <label>
          <b>Movie Block</b>
          <TextControl
            value={attributes.movieId}
            onChange={(value) => setAttributes({movieId: value})}
            placeholder="Enter a movie post ID."
            className="dashifen-movie-block-id"
          />
        </label>
      </div>
    );
  },

  save: () => null
});